<?php

namespace App\Filament\Crm\Resources\Invoices\Tables\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\FusedGroup;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class InvoiceDateFilter
{
    public static function make(): Filter
    {
        return Filter::make('invoice_date')
            ->schema([
                FusedGroup::make([
                    DatePicker::make('invoice_date_from'),
                    DatePicker::make('invoice_date_until'),
                ])->label(__('Date'))->columns(2),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['invoice_date_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '>=', $date),
                    )
                    ->when(
                        $data['invoice_date_until'],
                        fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '<=', $date),
                    );
            })
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if (isset($data['invoice_date_from'])) {
                    $indicators[] = __('From').': '.formatDate($data['invoice_date_from']);
                }

                if (isset($data['invoice_date_until'])) {
                    $indicators[] = __('Until').': '.formatDate($data['invoice_date_until']);
                }

                return $indicators;
            });
    }
}