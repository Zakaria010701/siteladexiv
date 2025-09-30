<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityAdvancedSettingsFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityDateFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityShiftsRepeater;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityTypeSelect;
use App\Models\AvailabilityType;
use Filament\Schemas\Components\Fieldset;

class UserCurrentAvailabilityFieldset
{
    public static function make(): Fieldset
    {
        return Fieldset::make()
            ->contained(false)
            ->relationship('currentAvailability')
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get) {
                $data['title'] = $get('name');
                $type = AvailabilityType::findOrFail($data['availability_type_id']);
                $data['color'] = $type->color;
                $data['is_hidden'] = $type->is_hidden;
                $data['is_all_day'] = $type->is_all_day;
                $data['is_background'] = $type->is_background;
                $data['is_background_inverted'] = $type->is_background_inverted;
                return $data;
            })
            ->schema([
                AvailabilityTypeSelect::make(),
                AvailabilityDateFieldset::make(),
                AvailabilityAdvancedSettingsFieldset::make(),
                AvailabilityShiftsRepeater::make(),
            ]);
    }
}
