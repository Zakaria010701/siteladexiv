<div class="cms-icon-text-block py-8">
    <div class="elementor-element" style="--widgets-spacing: 20px 20px;">
        @if($content['title'])
            <h2 class="text-2xl font-bold mb-12 text-center">{{ $content['title'] }}</h2>
        @endif

        <div class="flex justify-center" style="gap: 60px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 50px; padding: 40px 60px;">
            @foreach($content['items'] as $item)
                <div class="text-center w-48">
                    <i class="{{ $item['icon'] }} w-12 h-12 mb-4" style="color: #3991B3 !important; font-size: 30px; font-family: 'Font Awesome 5 Free';"></i>
                    @if(isset($item['header']) && $item['header'])
                        <h3 class="text-lg font-semibold mb-2" style="color: #3991B3 !important;">{{ $item['header'] }}</h3>
                    @endif
                    @if($item['type'] === 'phone')
                        <a href="tel:{{ $item['value'] }}" class="text-xl font-medium block" style="color: #3991B3 !important;">{{ $item['value'] }}</a>
                    @else
                        <p class="text-xl font-medium block" style="color: #3991B3 !important;">{{ $item['value'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>