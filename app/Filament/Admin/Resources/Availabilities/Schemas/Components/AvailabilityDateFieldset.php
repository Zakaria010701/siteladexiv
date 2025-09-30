<?php

namespace App\Filament\Admin\Resources\Availabilities\Schemas\Components;

use App\Models\Availability;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class AvailabilityDateFieldset
{
    public static function make(): Fieldset
    {
        return Fieldset::make()
            ->columns(3)
            ->schema([
                DatePicker::make('start_date')
                    ->default(today())
                    ->required(),
                Toggle::make('endless')
                    ->label(__('availability.endless'))
                    ->formatStateUsing(fn (?Availability $record) => is_null($record?->end_date)),
                DatePicker::make('end_date')
                    ->requiredUnless('endless', true)
                    ->dehydrateStateUsing(fn ($state, Get $get) => $get('endless') ? null : $state)
                    ->visibleJs(<<<'JS'
                        !$get('endless')
                    JS),
            ]);
    }
}
