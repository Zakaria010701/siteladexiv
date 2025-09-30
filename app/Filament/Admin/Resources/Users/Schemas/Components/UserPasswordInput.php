<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Forms\Components\TextInput;
use Hash;

class UserPasswordInput {

    public static function make(): TextInput
    {
        return TextInput::make('password')
            ->autocomplete('password')
            ->required(fn (string $operation) => $operation == 'create')
            ->password()
            ->revealable()
            ->dehydrated(fn (?string $state): bool => filled($state))
            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state));
    }
}
