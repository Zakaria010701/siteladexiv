<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $invoice->invoice_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css" media="screen">
        html {
            font-family: sans-serif;
            line-height: 1.0;
            margin: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.0;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 11px;
            margin: 32pt 57pt;
        }
        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        p {
            margin-top: 0;
            margin-bottom: 0rem;
        }
        strong {
            font-weight: bolder;
            font-size: 14px;
        }
        img {
            vertical-align: middle;
            border-style: none;
        }
        table {
            border-collapse: collapse;
        }
        th {
            text-align: inherit;
        }
        h4, .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }
        h4, .h4 {
            font-size: 1.5rem;
        }
        .table {
            width: 100%;
            margin-bottom: 0.3rem;
            color: #212529;
        }
        .table th,
        .table td {
            padding: 0.2rem;
            vertical-align: top;
        }

        #items {
            margin: 2rem 0;
        }
        #items thead {
            background-color: #276ebe;
            color: #ffffff;
        }
        #items tfoot {
            border-top: 5px double #276ebe;
        }


        #items th {
            padding: 0.5rem 0.3rem;
        }
        #items td {
            padding: 0.2rem 0.3rem;
        }

        #items tbody tr:first-child td,
        #items tfoot tr:first-child td {
            padding-top: 0.5rem;
        }

        #items tbody tr:last-child td {
            padding-bottom: 0.5rem;
        }
        .mt-5 {
            margin-top: 3rem !important;
        }
        .mb-1 {
            margin-bottom: 0.5rem !important;
        }
        .mb-2 {
            margin-bottom: 1rem !important;
        }
        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }
        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .px-2 {
            padding: 0 0.5rem;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .text-uppercase {
            text-transform: uppercase !important;
        }
        .text-primary {
            color: #276ebe;
        }
        .text-danger {
            color: #eb0606;
        }
        * {
            font-family: "DejaVu Sans";
        }
        body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
            line-height: 1.1;
        }
        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }
        .border-0 {
            border: none !important;
        }
        .footer {
            position: absolute;
            bottom: 0px
        }

        small {
            font-size: 9px;
        }

        .large {
            font-size: 13px;
        }
    </style>

</head>

<body>
    @if($invoice->status == \App\Enums\Invoices\InvoiceStatus::Canceled)
        <p class="text-danger text-uppercase"><strong>{{\App\Enums\Invoices\InvoiceStatus::Canceled->getLabel()}}</strong></p>
    @endif
    <x-pdf.company-logo></x-pdf.company-logo>

    <x-pdf.info-line></x-pdf.info-line>

    <table class="table pl-0">
        <tbody>
            <tr>
                {{-- Address --}}
                @if($invoice->recipient instanceof \App\Models\Customer)
                    <x-pdf.customer :customer="$invoice->recipient"></x-pdf.customer>
                @else
                    <td style="padding-left: 0">

                    </td>
                @endif
                {{-- Metadata --}}
                <td style="width: 30%">
                    <p style="margin-bottom: 0.3rem">
                        @lang('Invoice')
                        <span style="text-align: right; float: right">{{ $invoice->invoice_number }}</span>
                    </p>
                    <p style="margin-bottom: 0.3rem">
                        @lang('Invoice date')
                        <span style="text-align: right; float: right">{{ $invoice->invoice_date->format(getDateFormat())  }}</span>
                    </p>
                    <p style="margin-bottom: 0.3rem">
                        @lang('Due date')
                        <span style="text-align: right; float: right">{{ $invoice->due_date?->format(getDateFormat())  }}</span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 4rem">
        @if ($invoice->type == \App\Enums\Invoices\InvoiceType::Offer)
            <p class="mb-1 text-uppercase"><strong>@lang('Offer') {{ $invoice->invoice_number }}</strong></p>
        @else
            <p class="mb-1 text-uppercase"><strong>@lang('Invoice') {{ $invoice->invoice_number }}</strong></p>
        @endif
        <p class="mb-1 header">
            {!! nl2br($invoice->getHeader()) !!}
        </p>
    </div>

    {{-- Table --}}
    <table class="table" id="items">
        <thead>
            <tr>
                <th scope="col">{{ __('Invoice Item') }}</th>
                <th scope="col">{{ __('Description') }}</th>
                <th scope="col" class="text-center">{{ __('Quantity') }}</th>
                <th scope="col" class="text-right">{{ __('Price Per Unit') }}</th>
                <th scope="col" class="text-right">{{ __('Net Price') }}</th>
                <th scope="col" class="text-right">{{ __('Tax') }}</th>
                <th scope="col" class="text-right">{{ __('Sub Total') }}</th>
            </tr>
        </thead>
        <tbody>
            {{-- Items --}}
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->title }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->quantity }} {{ $item->unit ?? '' }}</td>
                <td class="text-right">
                    {{ formatMoney($item->unit_price) }}
                </td>
                <td class="text-right">
                    {{ formatMoney($item->net_price) }}
                </td>
                <td class="text-right">
                    {{ $item->tax_percentage }} %
                </td>
                <td class="text-right">
                    {{ formatMoney($item->sub_total) }}
                </td>
            </tr>
            @endforeach

        </tbody>
        <tfoot>
            {{-- Summary --}}
            @if($invoice->discount_total > 0)
                <tr>
                    <td colspan="6" class="pl-0">{{ __('Total Discount') }}</td>
                    <td class="pr-0 text-right">
                        {{ formatMoney($invoice->discount_total) }}
                    </td>
                </tr>
            @endif
            @if($invoice->net_total)
                <tr>
                    <td colspan="6" class="pl-0 text-primary">{{ __('Net Total') }}</td>
                    <td class="pr-0 text-right text-primary">
                        {{ formatMoney($invoice->net_total) }}
                    </td>
                </tr>
            @endif
            @if($invoice->tax_total > 0)
                <tr>
                    <td colspan="6" class="pl-0">{{ __('Tax Total') }}</td>
                    <td class="pr-0 text-right">
                        {{ formatMoney($invoice->tax_total) }}
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="6" class="pl-0 text-primary"><strong>{{ __('Gross Total') }}</strong></td>
                <td class="pr-0 text-right total-amount text-primary">
                    <strong>{{ formatMoney($invoice->gross_total) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    @if($invoice->notes)
        <p>
            {{ trans('invoices::invoice.notes') }}: {!! $invoice->notes !!}
        </p>
    @endif

    {{--<p>
        {{ trans('invoices::invoice.amount_in_words') }}: {{ $invoice->getTotalAmountInWords() }}
    </p>
    <p>
        {{ trans('invoices::invoice.pay_until') }}: {{ $invoice->getPayUntilDate() }}
    </p>--}}

    <p>
        {!! nl2br($invoice->getFooter()) !!}
    </p>

    <x-pdf.footer-table></x-pdf.footer-table>
</body>
</html>
