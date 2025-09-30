<?php

namespace App\Filament\Crm\Resources\Invoices\Tables\Groups;

use App\Models\Invoice;
use Filament\Tables\Grouping\Group;

class InvoiceRecipientGroup
{
    public static function make(): Group
    {
        return Group::make('recipient_id')
            ->label(__('Recipient'))
            ->getTitleFromRecordUsing(fn (Invoice $record): string => $record->getRecipientTitle())
            ->titlePrefixedWithLabel(false);
    }
}