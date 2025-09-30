<html lang="en">
<head>
    <title>{{__('Contract')}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style media="screen">
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
            margin: 36pt 57pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 0rem;
        }

        h1 {
            font-weight: bolder;
            font-size: 14px;
        }

        table {
            border-collapse: collapse;
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

        .table {
            width: 100%;
            margin-bottom: 0.3rem;
            color: #212529;
        }

        th {
            text-align: inherit;
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
    </style>
</head>
<body>
<x-pdf.company-logo></x-pdf.company-logo>
<x-pdf.info-line></x-pdf.info-line>
<table class="table pl-0">
    <tbody>
    <tr>
        <x-pdf.customer :customer="$contract->customer"></x-pdf.customer>
        {{-- Metadata --}}
        <td style="width: 30%">

        </td>
    </tr>
    </tbody>
</table>

<div style="margin-top: 4rem">
    <h1 class="mb-1 text-uppercase">@lang('Treatmentcontract')</h1>
</div>

<table class="table" id="items">
    <thead>
    <tr>
        <th scope="col" class="text-left">{{__('Unit price')}}</th>
        <th scope="col" class="text-left">{{__('Quantity')}}</th>
        <th scope="col" class="text-left">{{__('Total')}}</th>
        <th scope="col" class="text-left">{{__('Discount')}}</th>
        <th scope="col" class="text-left">{{__('Contract price')}}</th>
        <th scope="col" class="text-left">{{__('Saving')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($discounts as $discount)
        <tr>
            <td>{{ formatMoney($discount['unit_price']) }}</td>
            <td>{{ $discount['quantity'] }}</td>
            <td>{{ formatMoney($discount['total']) }}</td>
            <td>{{ $discount['percentage'] }}%</td>
            <td>{{ formatMoney($discount['contract_price']) }}</td>
            <td>{{ formatMoney($discount['saving']) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pdf.footer-table></x-pdf.footer-table>
</body>
</html>
