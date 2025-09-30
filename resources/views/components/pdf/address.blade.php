@isset($address)
    <p class="account-address">
        {{ $address->address }}<br>
        {{ $address->postcode }} {{ $address->location }}
    </p>
@endisset