<head>
    <title>@lang('Voucher')</title>
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
    <x-pdf.company-logo></x-pdf.company-logo>
    <x-pdf.info-line></x-pdf.info-line>

    <table class="table pl-0">
        <tbody>
            <tr>
                <x-pdf.customer :customer="$voucher->customer"></x-pdf.customer>
                {{-- Metadata --}}
                <td style="width: 30%">
                    <p style="margin-bottom: 0.3rem">
                        @lang('Voucher nr')
                        <span style="text-align: right; float: right">{{ $voucher->voucher_nr }}</span>
                    </p>
                    <p style="margin-bottom: 0.3rem">
                        @lang('Date')
                        <span style="text-align: right; float: right">{{ $voucher->created_at->format(getDateFormat()) }}</span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 4rem">
        <p class="mb-1 text-uppercase"><strong>@lang('Voucher Nr. :nr', ['nr' => $voucher->voucher_nr])</strong></p>
    </div>

    {{-- Table --}}
    <table class="table" id="items">
        <thead>
            <tr>
                <th scope="col">{{ __('Voucher') }}</th>
                <th scope="col" class="text-right">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            {{-- Items --}}
            <tr>
                <td>@lang('Voucher Nr. :nr', ['nr' => $voucher->voucher_nr])</td>
                <td class="text-right">{{ formatMoney($voucher->amount) }}</td>
            </tr>
        </tbody>
    </table>

    <x-pdf.footer-table></x-pdf.footer-table>
</body>
</html>
