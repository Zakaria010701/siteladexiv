
@php
    $customer = $getCustomer();
    $address = $customer?->addresses->first();
    $verifyAction = $getAction($getVerifyActionName());
@endphp
@if(!is_null($customer))
<div {{ $attributes }}>
    <table class="text-sm">
        <tr>
            <td>
                <x-filament::link href="{{ \App\Filament\Crm\Resources\Customers\CustomerResource::getUrl('edit', ['record' => $customer])}}" size="sm">
                    {{ $customer?->full_name }}
                </x-filament::link>
            </td>
        </tr>
        <tr>
            <td>{{ $customer?->primary_email }}</td>
        </tr>
        <tr>
            <td>{{ $customer?->primary_phone_number }}</td>
        </tr>
        @isset($address)
        <tr>
            <td>
                {{ $address->address }}<br>
                {{$address->postcode}} {{$address->location}}
            </td>
        </tr>
        @endisset
        <tr>
            <td>
                {{$verifyAction}}
                @if($customer->isVerified())<br><span class="text-xs text-gray-500">{{formatDateTime($customer->currentVerification?->created_at)}}</span>@endif
            </td>
        </tr>
    </table>
</div>
@endif
