@php
    $invoices = $getRecord()->customer->invoices()->open()->orderByDesc('invoice_date')->limit(5)->get();
@endphp
<div {{ $attributes }}>
    <table class="text-sm w-full">
        @foreach($invoices as $invoice)
            <tr>
                <td class="flex flex-row justify-between">
                    <div class="grow">
                        <x-filament::link href="{{\App\Filament\Crm\Resources\InvoiceResource::getUrl('edit', ['record' => $invoice])}}">
                            {{formatDate($invoice->invoice_date)}} {{$invoice->invoice_number}}
                        </x-filament::link>
                    </div>
                    <div class="text-right">
                        {{ formatMoney($invoice->gross_total) }}
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
</div>
