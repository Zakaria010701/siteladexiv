<?php

namespace App\Filament\Crm\Resources\Invoices\Schemas\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\FusedGroup;

class InvoiceNumberComponent
{
    public static function make(): Component
    {
        return FusedGroup::make([
            TextInput::make('series')
                ->required()
                ->readOnly()
                ->maxLength(255),
            TextInput::make('sequence')
                ->required()
                ->readOnly()
                ->numeric(),
        ])
            ->hiddenOn('edit')
            ->columns(2)
            ->label(__('Invoice number'));
    }
}