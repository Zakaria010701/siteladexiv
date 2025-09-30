<?php

namespace App\Filament\Crm\Resources\Invoices\Schemas\Components;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Carbon;

class InvoiceDatePicker
{
    public static function make(): DatePicker
    {
        return DatePicker::make('invoice_date')
            ->required()
            ->live()
            ->partiallyRenderComponentsAfterStateUpdated(['due_date'])
            ->afterStateUpdated(fn ($state, Set $set) => $set('due_date', Carbon::parse($state)->addDays(invoice()->due_after_days)->format('Y-m-d')))
            ->default(today());
    }
}