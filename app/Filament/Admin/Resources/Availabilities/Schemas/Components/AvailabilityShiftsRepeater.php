<?php

namespace App\Filament\Admin\Resources\Availabilities\Schemas\Components;

use App\Enums\TimeStep;
use App\Enums\Weekday;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Utilities\Get;

class AvailabilityShiftsRepeater
{
    public static function make(): Repeater
    {
        return Repeater::make('availabilityShifts')
            ->label(__('availability.shifts'))
            ->relationship('availabilityShifts')
            ->columnSpanFull()
            ->table([
                TableColumn::make(__('Start')),
                TableColumn::make(__('availability.target_minutes'))
                    ->markAsRequired(),
                TableColumn::make(__('Startdate'))
                    ->markAsRequired(),
                TableColumn::make(__('Room')),
                TableColumn::make(__('availability.repeat')),
                TableColumn::make(__('availability.weekday')),
            ])
            ->schema([
                TimePicker::make('start'),
                TimePicker::make('target_minutes')
                    ->required()
                    ->formatStateUsing(fn ($state) => formatTime($state))
                    ->dehydrateStateUsing(fn ($state) => deformatTime($state)),
                DatePicker::make('start_date')
                    ->required()
                    ->formatStateUsing(fn ($state, Get $get) => empty($state) ? $get('../../start_date') : $state)
                    ->minDate(fn (Get $get) => $get('../../start_date'))
                    ->maxDate(fn (Get $get) => $get('../../end_date')),
                Select::make('room')
                    ->relationship('room', 'name'),
                FusedGroup::make([
                    Select::make('repeat_step')
                        ->live()
                        ->required()
                        ->options(TimeStep::class)
                        ->default(TimeStep::Weeks->value),
                    TextInput::make('repeat_every')
                        ->required()
                        ->integer()
                        ->default(1),
                ])->columns(2),
                Select::make('weekday')
                    ->required(fn (Get $get) => $get('repeat_step') == TimeStep::Weeks->value)
                    ->options(Weekday::class),
            ]);
    }
}
