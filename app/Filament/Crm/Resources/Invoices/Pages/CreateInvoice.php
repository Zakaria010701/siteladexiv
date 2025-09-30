<?php

namespace App\Filament\Crm\Resources\Invoices\Pages;

use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Invoices\InvoiceType;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Filament\Crm\Resources\Invoices\Pages\Concerns\MutatesInvoiceItemData;
use App\Models\Customer;
use App\Models\Invoice;
use App\Support\Calculator;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateInvoice extends CreateRecord
{
    use MutatesInvoiceItemData;

    protected static string $resource = InvoiceResource::class;

    protected function afterFill()
    {
        $this->data['recipient_type'] = Customer::class;
        $this->data['type'] = InvoiceType::Invoice->value;
        $this->data['series'] = InvoiceType::Invoice->getSeries();
        $this->data['sequence'] = Invoice::where('series', InvoiceType::Invoice->getSeries())->count() + 1;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = InvoiceStatus::Open;
        $series = $data['series'];
        $sequence = Invoice::where('series', $series)->count() + 1;
        $data['sequence'] = $sequence;
        $data['invoice_number'] = sprintf('%s-%s', $series, Str::of($sequence)->padLeft(5, 0));
        return $this->mutateInvoiceItemData($data);
    }
}
