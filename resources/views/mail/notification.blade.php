<x-layout.mail>
    <x-slot:header>
        <tr>
            <td class="header">
                <h1 style="text-align: center">{{ $subject }}</h1>
            </td>
        </tr>
    </x-slot>

    {!! $header !!}

    {!! $content !!}

    {!! $footer !!}

    <x-slot:footer>
        <tr>
            <td>
            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td class="content-cell" align="center">
                        © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
                    </td>
                </tr>
            </table>
            </td>
        </tr>
    </x-slot>
</x-layout.mail>
