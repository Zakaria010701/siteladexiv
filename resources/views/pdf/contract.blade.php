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
                <table style="width: 100%">
                    <p style="margin-bottom: 0.3rem">
                        @lang('Date')
                        <span style="text-align: right; float: right">{{ formatDate($contract->date) }}</span>
                    </p>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <div style="margin-top: 4rem">
        <h1 class="mb-1 text-uppercase">@lang('Package Booking') {{ $contract->id }}</h1>
    </div>

    <table class="table" id="items">
        <thead>
        <tr>
            <th scope="col">{{ __('Services') }}</th>
            <th scope="col" class="text-right">{{ __('Treatment count') }}</th>
            <th scope="col" class="text-right">{{ __('Treatments done') }}</th>
            <th scope="col" class="text-right">{{ __('Total') }}</th>
        </tr>
        </thead>
        <tbody>
        {{-- Items --}}
        @foreach($contract->contractServices as $service)
            <tr>
                <td>{{ $service->service->name }}</td>
                <td class="text-right">{{ $contract->treatment_count }}</td>
                <td class="text-right">{{ $service->getTreatmentsDone() }}</td>
                <td class="text-right">{{ formatMoney($service->price) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="pl-0 text-primary">@lang('Gross Total')</td>
            <td class="pr-0 text-right text-primary">{{ formatMoney($contract->price) }}</td>
        </tr>
        </tfoot>
    </table>
    <x-pdf.footer-table></x-pdf.footer-table>
</body>
</html>
