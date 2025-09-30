<?php

namespace App\Filament\Crm\Resources\Invoices\Schemas\Components;

use App\Enums\Invoices\InvoiceType;
use App\Models\Invoice;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;

class InvoiceTypeSelect
{
    public static function make(): Select
    {
        return Select::make('type')
            ->required()
            ->columnStart(1)
            ->live()
            ->partiallyRenderComponentsAfterStateUpdated(['series', 'sequence'])
            ->disabledOn('edit')
            ->options(InvoiceType::class)
            ->afterStateUpdated(function (?InvoiceType $state, Set $set) {
                if (is_null($state)) {
                    return;
                }
                $set('series', $state->getSeries());
                $set('sequence', Invoice::where('series', $state->getSeries())->count() + 1);
            });
    }
}