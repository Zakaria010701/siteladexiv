<?php

namespace App\Filament\Crm\Resources\Invoices\Tables\Columns;

use App\Models\Customer;
use App\Models\Invoice;
use Filament\Tables\Columns\TextColumn;

class InvoiceRecipientColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('recipient')
            ->searchable(query: fn (Builder $query, string $search) => $query->whereHasMorph(
                relation: 'recipient',
                types: [Customer::class],
                callback: fn (Builder $query) => $query
                    ->where('lastname', 'like', "%$search%")
                    ->orWhere('firstname', 'like', "%$search%")
            ))
            ->url(fn (Invoice $record) => $record->getRecipientUrl())
            ->formatStateUsing(fn (Invoice $record) => $record->getRecipientTitle());
    }
}