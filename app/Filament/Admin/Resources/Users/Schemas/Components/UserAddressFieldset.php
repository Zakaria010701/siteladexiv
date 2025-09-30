<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

class UserAddressFieldset {

    public static function make(): Fieldset
    {
        return Fieldset::make(__('Address'))
            ->columns(3)
            ->columnSpanFull()
            ->schema([
                TextInput::make('street'),
                TextInput::make('postcode'),
                TextInput::make('city'),
            ]);
    }
}
