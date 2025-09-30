<?php

namespace App\Filament\Crm\Resources\Invoices\Schemas\Components;

use App\Forms\Components\FusedMorphToSelect;
use App\Models\Appointment;
use App\Models\Customer;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvoiceSourceSelect
{
    public static function make(): MorphToSelect
    {
        return FusedMorphToSelect::make('source')
            ->hiddenJs(<<<'JS'
                !$get('recipient_id')
            JS)
            ->types([
                Type::make(Appointment::class)
                    ->label(__('Appointment'))
                    ->searchColumns(['date'])
                    ->modifyOptionsQueryUsing(function (Builder $query, Get $get) {
                        return $query->when(
                            $get('recipient_type') == Customer::class,
                            fn (Builder $query) => $query->where('customer_id', $get('recipient_id'))
                        );
                    })
                    ->getOptionLabelFromRecordUsing(fn (Appointment $record) => $record->title),
            ]);
    }
}
