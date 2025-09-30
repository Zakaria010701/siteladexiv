<td {{ $attributes->merge(["style" => "padding-left: 0"]) }}>
    @isset($customer)
                <p style="margin-bottom: 0.3rem;">{{ $customer->full_name }}</p>
        @php
            $address = $customer->addresses->first();
        @endphp
        @isset($address)
                <p style="margin-bottom: 0.3rem;">{{ $address->address }}</p>
                <p style="margin-bottom: 0.3rem;">{{ $address->postcode }} {{ $address->location }}</p>
        @endisset
    @endisset
</td>
