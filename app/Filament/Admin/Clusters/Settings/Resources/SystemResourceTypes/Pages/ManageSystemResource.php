<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Forms\Components\TableRepeater\Header;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Schemas\Components\Fieldset;
use App\Enums\Resources\ResourceFieldType;
use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\SystemResourceTypeResource;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use App\Forms\Components\TableRepeater;
use App\Models\AvailabilityType;
use App\Models\ResourceField;
use App\Models\SystemResource;
use App\Models\SystemResourceType;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Google\Service\HangoutsChat\DateInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ManageSystemResource extends ManageRelatedRecords
{
    protected static string $resource = SystemResourceTypeResource::class;

    protected static string $relationship = 'systemResources';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Resources');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Repeater::make('availabilities')
                    ->relationship('availabilities')
                    ->columnSpanFull()
                    ->columns(4)
                    ->collapsed()
                    ->itemLabel(fn (array $state) => sprintf('%s - %s', formatDate($state['start_date']), is_null($state['end_date']) ? __('Unlimited') : formatDate($state['end_date'])))
                    ->deleteAction(fn (Action $action) => $action
                        ->requiresConfirmation()
                        ->modalHeading(__('filament-actions::modal.confirmation'))
                        ->modalDescription(__('Deleting this entry may alter the calender in ways that can not be restored!'))
                    )
                    ->addAction(fn (Action $action, Get $get) => $action
                        ->action(function (Action $action, Repeater $component) use ($get): void {
                            $newUuid = $component->generateUuid();

                            $items = $component->getState();

                            $availabilities = collect($items);

                            $startDate = today()->format('Y-m-d');
                            if($availabilities->isNotEmpty()) {
                                if($availabilities->whereNotNull('end_date')->isNotEmpty()) {
                                    $startDate = Carbon::parse($availabilities->whereNotNull('end_date')->sortByDesc('end_date')->first()['end_date'])->addDay()->format('Y-m-d');
                                } else {
                                    Notification::make()
                                        ->title(__('Please first add an end date to the last availability!'))
                                        ->danger()
                                        ->color('danger')
                                        ->send();
                                    $action->cancel();
                                }
                            }

                            if ($newUuid) {
                                $items[$newUuid] =  [];
                            } else {
                                $items[] = [];
                            }

                            $component->state($items);

                            $component->getChildComponentContainer($newUuid ?? array_key_last($items))->fill([
                                'title' => $get('name'),
                                'color' => '#a0a0a0',
                                'start_date' => $startDate,
                            ]);

                            $component->collapsed(false, shouldMakeComponentCollapsible: false);

                            $component->callAfterStateUpdated();
                        }))
                    ->schema([
                        DatePicker::make('start_date')
                            ->helperText(__('The date from which this availability is valid'))
                            ->live()
                            ->required()
                            ->default(today())
                            ->columnSpan(2),
                        DatePicker::make('end_date')
                            ->helperText(__('The date until which this availability is valid. (Leave empty for no limit)'))
                            ->live(),
                        Select::make('availability_type_id')
                            ->required()
                            ->live()
                            ->relationship('availabilityType', 'name')
                            ->afterStateUpdated(function ($state, Set $set) {
                                $type = AvailabilityType::findOrFail($state);
                                $set('color', $type->color);
                                $set('is_hidden', $type->is_hidden);
                                $set('is_all_day', $type->is_all_day);
                                $set('is_background', $type->is_background);
                                $set('is_background_inverted', $type->is_background_inverted);
                            }),
                        TextInput::make('title')
                            ->helperText(__('The text that will be displayed on the events in the calendar'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        ColorPicker::make('color')
                            ->helperText(__('The color of the events in the calendar'))
                            ->required()
                            ->default('#a0a0a0')
                            ->columnSpan(2),
                        Toggle::make('is_hidden')
                            ->live()
                            ->helperText(__('Hide the events from the calendar'))
                            ->inline(false),
                        Toggle::make('is_all_day')
                            ->helperText(__('Enable to display the events in the top part of the calendar'))
                            ->visible(fn (Get $get) => !$get('is_hidden'))
                            ->default(true)
                            ->inline(false),
                        Toggle::make('is_background')
                            ->helperText(__('Enable to block the calendar during this events time'))
                            ->visible(fn (Get $get) => !$get('is_hidden'))
                            ->live()
                            ->default(false)
                            ->inline(false),
                        Toggle::make('is_background_inverted')
                            ->helperText(__('Enable to open the calendar during this events time'))
                            ->visible(fn (Get $get) => $get('is_background') && !$get('is_hidden'))
                            ->default(false)
                            ->inline(false),
                        TableRepeater::make('availabilityShifts')
                            ->relationship('availabilityShifts')
                            ->columnSpanFull()
                            ->headers([
                                Header::make('room'),
                                Header::make('start')
                                    ->markAsRequired(),
                                Header::make('target_minutes')
                                    ->markAsRequired(),
                                Header::make('start_date')
                                    ->markAsRequired(),
                                Header::make('repeat_step')
                                    ->label(__('Repeat')),
                                Header::make('repeat_every')
                                    ->label(__('Every')),
                                Header::make('weekday'),
                            ])
                            ->schema([
                                Select::make('room')
                                    ->relationship('room', 'name'),
                                TimePicker::make('start')
                                    ->required(),
                                TimePicker::make('target_minutes')
                                    ->required()
                                    ->formatStateUsing(fn ($state) => formatTime($state))
                                    ->dehydrateStateUsing(fn ($state) => deformatTime($state)),
                                DatePicker::make('start_date')
                                    ->required()
                                    ->formatStateUsing(fn ($state, Get $get) => empty($state) ? $get('../../start_date') : $state)
                                    ->minDate(fn (Get $get) => $get('../../start_date'))
                                    ->maxDate(fn (Get $get) => $get('../../end_date')),
                                Select::make('repeat_step')
                                    ->live()
                                    ->required()
                                    ->options(TimeStep::class)
                                    ->default(TimeStep::Weeks->value),
                                TextInput::make('repeat_every')
                                    ->required()
                                    ->integer()
                                    ->default(1),
                                Select::make('weekday')
                                    ->required(fn (Get $get) => $get('repeat_step') == TimeStep::Weeks->value)
                                    ->options(Weekday::class),
                            ]),
                        TableRepeater::make('availabilityAbsences')
                            ->relationship('availabilityAbsences')
                            ->columnSpanFull()
                            ->headers([
                                Header::make('start_date')
                                    ->markAsRequired(),
                                Header::make('end_date')
                                    ->markAsRequired(),
                            ])
                            ->schema([
                                DatePicker::make('start_date')
                                    ->required()
                                    ->minDate(fn (Get $get) => $get('../../start_date'))
                                    ->maxDate(fn (Get $get) => $get('../../end_date')),
                                DatePicker::make('end_date')
                                    ->required()
                                    ->minDate(fn (Get $get) => $get('../../start_date'))
                                    ->maxDate(fn (Get $get) => $get('../../end_date')),
                            ]),
                        TableRepeater::make('availabilityExceptions')
                            ->relationship('availabilityExceptions')
                            ->columnSpanFull()
                            ->headers([
                                Header::make('room'),
                                Header::make('date')
                                    ->markAsRequired(),
                                Header::make('start')
                                    ->markAsRequired(),
                                Header::make('target_minutes')
                                    ->markAsRequired(),
                            ])
                            ->schema([
                                Select::make('room')
                                    ->relationship('room', 'name'),
                                DatePicker::make('date')
                                    ->required()
                                    ->minDate(fn (Get $get) => $get('../../start_date'))
                                    ->maxDate(fn (Get $get) => $get('../../end_date')),
                                TimePicker::make('start')
                                    ->required(),
                                TimePicker::make('target_minutes')
                                    ->required()
                                    ->formatStateUsing(fn ($state) => formatTime($state))
                                    ->dehydrateStateUsing(fn ($state) => deformatTime($state)),
                            ]),
                    ]),
                $this->getSystemResourceCustomFields(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(fn(array $data) => $this->createSystemResource($data))
                    ->after(function () {
                        if(Cache::supportsTags()) {
                            Cache::tags(['resource', 'events'])->flush();
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-m-eye')
                    ->url(fn (SystemResource $record) => SystemResourceResource::getUrl('edit', ['record' => $record])),
                EditAction::make()
                        ->iconButton()
                        ->modalWidth(Width::SevenExtraLarge)
                        ->using(fn(array $data, SystemResource $record) => $this->updateSystemResource($data, $record))
                        ->after(function () {
                            if(Cache::supportsTags()) {
                                Cache::tags(['resource', 'events'])->flush();
                            }
                        }),
                ActionGroup::make([
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    private function getSystemResourceCustomFields(): Fieldset
    {
        return Fieldset::make('custom_fields')
            ->label(__('Custom fields'))
            ->schema(function () {
                /** @var Collection $fields */
                $fields = $this->getRecord()->resourceFields;
                return $fields->map(function (ResourceField $field) {
                    $input = match($field->type) {
                        ResourceFieldType::Number => TextInput::make($field->name)
                            ->numeric(),
                        ResourceFieldType::Date => DatePicker::make($field->name),
                        ResourceFieldType::DateTime => DateTimePicker::make($field->name),
                        ResourceFieldType::Toggle => Toggle::make($field->name)
                            ->inline(false),
                        ResourceFieldType::Select => Select::make($field->name)
                            ->options(collect($field->options)->mapWithKeys(fn (array $value) => [$value['value'] => $value['label']])->toArray()),
                        ResourceFieldType::Checkboxes => CheckboxList::make($field->name)
                            ->options(collect($field->options)->mapWithKeys(fn (array $value) => [$value['value'] => $value['label']])->toArray()),
                        default => TextInput::make($field->name),
                    };
                    return $input
                        ->label($field->label)
                        ->required($field->required)
                        ->disabled($field->disabled)
                        ->formatStateUsing(function (?SystemResource $record) use ($field) {
                            if(is_null($record)) {
                                return $field->default_value;
                            }
                            return $record->getValue($field);
                        });
                })->toArray();
            });
    }

    private function createSystemResource(array $data) : SystemResource
    {
        $relationship = $this->getRelationship();
        /** @var Collection $fields */
        $fields = $this->getRecord()->resourceFields;


        /** @var SystemResource $record */
        $record = $relationship->create($data);

        $fields->each(function (ResourceField $field) use ($data, $record) {
            $value = $data[$field->name] ?? null;

            $record->resourceValues()->create([
                'resource_field_id' => $field->id,
                'value' => $value,
            ]);
        });

        return $record;
    }

    private function updateSystemResource(array $data, SystemResource $record): void
    {
        /** @var Collection $fields */
        $fields = $this->getRecord()->resourceFields;

        $record->update($data);

        $fields->each(function (ResourceField $field) use ($data, $record) {
            $value = $data[$field->name] ?? null;

            $record->resourceValues()->updateOrCreate([
                'resource_field_id' => $field->id,
            ], [
                'value' => $value,
            ]);
        });
    }
}
