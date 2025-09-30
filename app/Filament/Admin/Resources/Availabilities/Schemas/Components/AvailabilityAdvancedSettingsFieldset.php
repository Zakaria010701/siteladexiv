<?php

namespace App\Filament\Admin\Resources\Availabilities\Schemas\Components;

use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;

class AvailabilityAdvancedSettingsFieldset
{
    public static function make(): Fieldset
    {
        return Fieldset::make(__('availability.advanced_settings'))
            ->visible(AvailabilityResource::can('admin'))
            ->dehydratedWhenHidden()
            ->columns(5)
            ->schema([
                Toggle::make('is_hidden')
                    ->label(__('availability.is_hidden'))
                    ->columnStart(1)
                    ->required(),
                Toggle::make('is_all_day')
                    ->label(__('availability.is_all_day'))
                    ->required(),
                Toggle::make('is_background')
                    ->label(__('availability.is_background'))
                    ->required(),
                Toggle::make('is_background_inverted')
                    ->label(__('availability.is_background_inverted'))
                    ->required(),
                ColorPicker::make('color')
                    ->required()
                    ->default('#a0a0a0'),
            ]);
    }
}
