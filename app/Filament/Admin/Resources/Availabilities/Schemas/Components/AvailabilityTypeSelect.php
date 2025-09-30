<?php

namespace App\Filament\Admin\Resources\Availabilities\Schemas\Components;

use App\Models\AvailabilityType;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;

class AvailabilityTypeSelect
{
    public static function make(): Select
    {
        return Select::make('availability_type_id')
            ->label(__('availability.type'))
            ->relationship('availabilityType', 'name')
            ->required()
            ->live()
            ->partiallyRenderComponentsAfterStateUpdated(['color', 'is_hidden', 'is_all_day', 'is_background', 'is_background_inverted'])
            ->afterStateUpdated(function ($state, Set $set) {
                $type = AvailabilityType::findOrFail($state);
                $set('color', $type->color);
                $set('is_hidden', $type->is_hidden);
                $set('is_all_day', $type->is_all_day);
                $set('is_background', $type->is_background);
                $set('is_background_inverted', $type->is_background_inverted);
            });
    }
}
