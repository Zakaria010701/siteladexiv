<?php

namespace App\Filament\Crm\Resources\Invoices\Pages\Concerns;

use App\Support\Calculator;

trait MutatesInvoiceItemData
{
    private function mutateInvoiceItemData(array $data): array
    {
        $items = collect($this->form->getRawState()['items'])->map(fn (array $item) => [
            'tax' => Calculator::getTaxAmmount($item['unit_price'] * $item['quantity'], $item['tax_percentage'], 2, false),
            'sub_total' => $item['unit_price'] * $item['quantity'],
        ]);
        $tax = $items->sum('tax');
        $gross = $items->sum('sub_total');
        $data['base_total'] = $gross - $tax;
        $data['tax_total'] = $tax;
        $data['gross_total'] = $gross;
        $data['net_total'] = $gross - $tax;
        return $data;
    }
}