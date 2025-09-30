<?php

namespace App\Filament\Crm\Resources\Customers\Schemas\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

class CustomerAccountsRepeater
{
    public static function make(): Repeater
    {
        return Repeater::make('accounts')
            ->label('')
            ->relationship('accounts')
            ->table([
                TableColumn::make(__('Name')),
                TableColumn::make(__('Iban')),
            ])
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('iban')
                    ->required()
                    ->maxLength(34)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
