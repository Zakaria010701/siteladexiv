<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

class UserNameFieldset {

    public static function make(): Fieldset
    {
        return Fieldset::make()
            ->columns(3)
            ->schema([
                TextInput::make('name')
                    ->autocomplete('username')
                    ->unique(ignoreRecord: true)
                    ->belowContent(__('This is the name used for the login, it needs to be unique.'))
                    ->required(),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('lastname')
                    ->required(),
            ]);
    }
}
