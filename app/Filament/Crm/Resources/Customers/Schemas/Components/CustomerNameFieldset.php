<?php

namespace App\Filament\Crm\Resources\Customers\Schemas\Components;

use App\Enums\Gender;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

class CustomerNameFieldset
{
    public static function make(): Fieldset
    {
        return Fieldset::make(__('Name'))
            ->schema([
                TextInput::make('title')
                    ->maxLength(255),
                Select::make('gender')
                    ->required()
                    ->options(Gender::class),
                TextInput::make('firstname')
                    ->required()
                    ->maxLength(255),
                TextInput::make('lastname')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
