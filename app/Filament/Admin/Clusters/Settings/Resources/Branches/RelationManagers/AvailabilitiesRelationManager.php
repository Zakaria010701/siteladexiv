<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Branches\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use App\Forms\Components\TableRepeater\Header;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use App\Forms\Components\TableRepeater;
use App\Models\Availability;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvailabilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'availabilities';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->helperText(__('The date from which this availability is valid'))
                    ->required()
                    ->default(today()),
                DatePicker::make('end_date')
                    ->helperText(__('The date until which this availability is valid. (Leave empty for no limit)')),
                /*Forms\Components\TextInput::make('title')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->helperText(__('The text that will be displayed on the events in the calendar'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),*/
                ColorPicker::make('color')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->helperText(__('The color of the events in the calendar'))
                    ->required()
                    ->default('#a0a0a0')
                    ->columnSpan(2),
                Toggle::make('is_hidden')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->live()
                    ->helperText(__('Hide the events from the calendar'))
                    ->inline(false),
                Toggle::make('is_all_day')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->helperText(__('Enable to display the events in the top part of the calendar'))
                    ->visible(fn (Get $get) => !$get('is_hidden'))
                    ->default(true)
                    ->inline(false),
                Toggle::make('is_background')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->helperText(__('Enable to block the calendar during this events time'))
                    ->visible(fn (Get $get) => !$get('is_hidden'))
                    ->live()
                    ->default(false)
                    ->inline(false),
                Toggle::make('is_background_inverted')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->helperText(__('Enable to open the calendar during this events time'))
                    ->visible(fn (Get $get) => $get('is_background') && !$get('is_hidden'))
                    ->default(false)
                    ->inline(false),
                TableRepeater::make('availabilityShifts')
                    ->relationship('availabilityShifts')
                    ->columnSpanFull()
                    ->cloneable()
                    ->headers([
                        Header::make('room'),
                        Header::make('start'),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->columns([
                TextColumn::make('start_date'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data, CreateAction $action): array {
                        $data['title'] = $this->getOwnerRecord()->name;

                        $data['color'] = '#23ccbb';
                        $data['is_hidden'] = false;
                        $data['is_all_day'] = false;
                        $data['is_background'] = true;
                        $data['is_background_inverted'] = true;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-m-eye')
                    ->url(fn (Availability $record) => AvailabilityResource::getUrl('edit', ['record' => $record])),
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
