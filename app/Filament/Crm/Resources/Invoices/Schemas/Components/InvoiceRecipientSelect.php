<?php

namespace App\Filament\Crm\Resources\Invoices\Schemas\Components;

use App\Forms\Components\FusedMorphToSelect;
use App\Models\Customer;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;

class InvoiceRecipientSelect
{
    public static function make(): MorphToSelect
    {
        return FusedMorphToSelect::make('recipient')
            ->required()
            ->searchable()
            ->types([
                Type::make(Customer::class)
                    ->label(__('Customer'))
                    ->searchColumns(['firstname', 'lastname'])
                    ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->full_name),
            ]);
    }
}