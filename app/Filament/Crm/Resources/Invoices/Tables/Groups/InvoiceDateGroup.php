<?php

namespace App\Filament\Crm\Resources\Invoices\Tables\Groups;

use App\Models\Invoice;
use Filament\Tables\Grouping\Group;

class InvoiceDateGroup
{
    public static function make(): Group
    {
        return Group::make('invoice_date')
            ->label(__('Date'))
            ->getTitleFromRecordUsing(fn (Invoice $record): string => formatDate($record->invoice_date))
            ->titlePrefixedWithLabel(false);
    }
}