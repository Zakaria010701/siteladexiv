<div {{ $attributes }}>
    <table class="text-xs">
        <tr>
            <td class="px-1">{{ __('Last changed') }}</td>
            <td class="px-1">{{ formatDateTime($getRecord()->lastActivity?->created_at)  }}</td>
        </tr>
        <tr>
            <td class="px-1">{{ __('Changed by') }}</td>
            <td class="px-1">{{ $getRecord()->lastActivity?->getCauserLabel() }}</td>
        </tr>
        <tr>
            <td class="px-1">{{ __('Approved at') }}</td>
            <td class="px-1">{{ formatDateTime($getRecord()->approved_at) }}</td>
        </tr>
        <tr>
            <td class="px-1">{{ __('Done at') }}</td>
            <td class="px-1">{{ formatDateTime($getRecord()->done_at) }}</td>
        </tr>
        <tr>
            <td class="px-1">{{ __('Check in') }}</td>
            <td class="px-1">{{ formatDateTime($getRecord()->check_in_at) }}</td>
        </tr>
        <tr>
            <td class="px-1">{{ __('Check out') }}</td>
            <td class="px-1">{{ formatDateTime($getRecord()->check_out_at) }}</td>
        </tr>
    </table>
</div>
