<p {{ $attributes->merge(["style" => "margin-top: 7.5rem; padding: 0.2rem; margin-bottom: 1rem"]) }}>
    <small>{{ company()->name }} | {{ company()->address }} | {{ company()->postcode }} {{ company()->location }}</small>
</p>