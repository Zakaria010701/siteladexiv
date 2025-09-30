<?php

namespace App\Filament\Admin\Resources\SystemResources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use App\Forms\Components\TableRepeater\Header;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Admin\Resources\SystemResources\RelationManagers\AvailabilitiesRelationManager;
use App\Filament\Admin\Resources\SystemResources\Pages\ListSystemResources;
use App\Filament\Admin\Resources\SystemResources\Pages\CreateSystemResource;
use App\Filament\Admin\Resources\SystemResources\Pages\EditSystemResource;
use App\Filament\Admin\Resources\SystemResources\Pages\ListSystemResourceDependencies;
use App\Enums\Resources\ResourceFieldType;
use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\SystemResourceTypeResource;
use App\Filament\Admin\Resources\SystemResourceResource\Pages;
use App\Filament\Admin\Resources\SystemResourceResource\RelationManagers;
use App\Forms\Components\TableRepeater;
use App\Models\Availability;
use App\Models\AvailabilityType;
use App\Models\ResourceField;
use App\Models\SystemResource;
use App\Models\SystemResourceType;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Google\Service\Docs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;

class SystemResourceResource extends Resource
{
    protected static ?string $model = SystemResource::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn(Unique $rule, Get $get) => $rule->where('system_resource_type_id', $get('system_resource_type_id'))
                    )
                    ->maxLength(255),
                Select::make('system_resource_type_id')
                    ->live()
                    ->required()
                    ->disabledOn('edit')
                    ->relationship('systemResourceType', 'name')
                    ->afterStateUpdated(function (Select $component) {
                        $component->getContainer()
                            ->getComponent('custom_fields')
                            ?->getChildSchema()
                            ?->fill();
                        $component->getContainer()
                            ->getComponent('dependencies')
                            ?->getChildSchema()
                            ?->fill();
                    }),
                Fieldset::make('custom_fields')
                    ->key('custom_fields')
                    ->label(__('Custom fields'))
                    ->visible(fn (?SystemResource $record, Get $get) => isset($record) ? $record->systemResourceType->resourceFields->isNotEmpty() : SystemResourceType::find($get('system_resource_type_id'))?->resourceFields?->isNotEmpty())
                    ->schema(function (?SystemResource $record, Get $get) {
                        /** @var Collection $fields */
                        $fields = isset($record) ? $record->systemResourceType->resourceFields : SystemResourceType::find($get('system_resource_type_id'))?->resourceFields;
                        if (is_null($fields) || $fields->isEmpty()) {
                            return [];
                        }
                        return $fields->map(function (ResourceField $field) {
                            $input = match ($field->type) {
                                ResourceFieldType::Number => TextInput::make($field->name)
                                    ->numeric(),
                                ResourceFieldType::Date => DatePicker::make($field->name),
                                ResourceFieldType::DateTime => DateTimePicker::make($field->name),
                                ResourceFieldType::Toggle => Toggle::make($field->name)
                                    ->inline(false),
                                ResourceFieldType::Select => Select::make($field->name)
                                    ->options(collect($field->options)->mapWithKeys(fn(array $value
                                    ) => [$value['value'] => $value['label']])->toArray()),
                                ResourceFieldType::Checkboxes => CheckboxList::make($field->name)
                                    ->options(collect($field->options)->mapWithKeys(fn(array $value
                                    ) => [$value['value'] => $value['label']])->toArray()),
                                default => TextInput::make($field->name),
                            };
                            return $input
                                ->label($field->label)
                                ->required($field->required)
                                ->disabled($field->disabled)
                                ->formatStateUsing(function ($state, ?SystemResource $record) use ($field) {
                                    if (is_null($record)) {
                                        return $field->default_value ?? $state;
                                    }
                                    return $record->getValue($field);
                                });
                        })->toArray();
                    }),
                Fieldset::make('dependencies')
                    ->key('dependencies')
                    ->columns(3)
                    ->schema(function (Get $get) {
                        $rType = SystemResourceType::find($get('system_resource_type_id'));

                        if(is_null($rType)) {
                            return [];
                        }

                        $schema = [
                            CheckboxList::make('branches')
                                ->columns(2)
                                ->bulkToggleable()
                                ->hidden(!$rType->depends_on_branch)
                                ->relationship('branchDependencies', 'name'),
                            CheckboxList::make('rooms')
                                ->columns(2)
                                ->bulkToggleable()
                                ->hidden(!$rType->depends_on_room || $rType->depends_on_availability)
                                ->relationship('roomDependencies', 'name'),
                            Actions::make([
                                Action::make('availability')
                                    ->modalWidth(Width::SevenExtraLarge)
                                    ->fillForm(function (SystemResource $record) {
                                        return [
                                            'availabilities' => $record->availabilities->mapWithKeys(fn (Availability $availability) => [
                                                "record-$availability->id" => $availability->attributesToArray()
                                            ])->toArray(),
                                        ];
                                    })
                                    ->schema([self::getAvailabilityRepeater()])
                                    ->action(function () {
                                        Notification::make()
                                            ->title(__('Action success'))
                                            ->success()
                                            ->send();
                                    }),
                            ]),
                            CheckboxList::make('users')
                                ->columns(2)
                                ->bulkToggleable()
                                ->hidden(!$rType->depends_on_user)
                                ->relationship('userDependencies', 'name'),
                            CheckboxList::make('categories')
                                ->live()
                                ->columns(2)
                                ->bulkToggleable()
                                ->hidden(!$rType->depends_on_category)
                                ->relationship('categoryDependencies', 'name'),
                            CheckboxList::make('services')
                                ->columns(2)
                                ->bulkToggleable()
                                ->hidden(!$rType->depends_on_category)
                                ->relationship('serviceDependencies', 'name', modifyQueryUsing: fn (Builder $query, Get $get) => $query->whereIn('category_id', $get('categories') ?? [])),

                        ];

                        $types = $rType->systemResourceTypeDependencies
                            ->map(fn (SystemResourceType $type) => CheckboxList::make($type->name)
                                ->label($type->name)
                                ->columns(2)
                                ->bulkToggleable()
                                ->relationship(
                                    name:'systemResourceDependencies',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (Builder $query) => $query->where('system_resource_type_id', $type->id)
                                )
                            )
                            ->toArray();

                        $schema = array_merge($schema, $types);

                        return $schema;
                    }),
                KeyValue::make('meta')
                    ->columnSpanFull(),
            ]);
    }

    private static function getAvailabilityRepeater()
    {
        return Repeater::make('availabilities')
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
                        'start_date' => $startDate,
                    ]);

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);

                    $component->callAfterStateUpdated();
                }))
             ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                $type = AvailabilityType::findOrFail($data['availability_type_id']);

                $data['color'] = $type->color;
                $data['is_hidden'] = $type->is_hidden;
                $data['is_all_day'] = $type->is_all_day;
                $data['is_background'] = $type->is_background;
                $data['is_background_inverted'] = $type->is_background_inverted;

                return $data;
            })
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
                    ->relationship('availabilityType', 'name'),
                TextInput::make('title')
                    ->helperText(__('The text that will be displayed on the events in the calendar'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
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
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('systemResourceType.name')
                    ->url(fn (SystemResource $record) => SystemResourceTypeResource::getUrl('edit', ['record' => $record->systemResourceType]))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->defaultGroup('systemResourceType.id')
            ->groups([
                Group::make('systemResourceType.id')
                    ->label(__('Type'))
                    ->getTitleFromRecordUsing(fn (SystemResource $record): string => $record->systemResourceType->name),
            ])
            ->filters([
                SelectFilter::make('system_resource_type_id')
                    ->relationship('systemResourceType', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label(__('Type')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton(),
                ActionGroup::make([
                    ActionGroup::make([
                        DeleteAction::make(),
                        RestoreAction::make(),
                        ForceDeleteAction::make(),
                    ])->dropdown(false),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AvailabilitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemResources::route('/'),
            'create' => CreateSystemResource::route('/create'),
            'edit' => EditSystemResource::route('/{record}/edit'),
            'dependencies' => ListSystemResourceDependencies::route('/{record}/dependencies'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
