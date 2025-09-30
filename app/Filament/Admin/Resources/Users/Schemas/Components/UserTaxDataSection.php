<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class UserTaxDataSection {

    public static function make(): Section
    {
        return Section::make(__('Tax data'))
            ->columns(3)
            ->relationship('userDetails')
            ->schema([
                TextInput::make('social_security_number'),
                TextInput::make('health_insurance'),
                TextInput::make('health_insurance_number'),
                TextInput::make('iban'),
                TextInput::make('tax_id'),
                TextInput::make('tax_class'),
                TextInput::make('religious_affiliation'),
                Toggle::make('children'),
            ]);
    }

}
