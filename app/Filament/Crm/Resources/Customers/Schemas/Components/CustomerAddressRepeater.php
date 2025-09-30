<?php

namespace App\Filament\Crm\Resources\Customers\Schemas\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;

class CustomerAddressRepeater
{
    public static function make(): Repeater
    {
        return Repeater::make('address')
            ->label('')
            ->relationship('addresses')
            ->defaultItems(0)
            ->table([
                TableColumn::make(__('Location')),
                TableColumn::make(__('Zip code')),
                TableColumn::make(__('Address')),
            ])
            ->schema([
                TextInput::make('location'),
                TextInput::make('postcode'),
                TextInput::make('address'),
            ]);
    }
}
