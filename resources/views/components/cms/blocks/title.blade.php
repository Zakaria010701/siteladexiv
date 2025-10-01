@if($position === 'center')
<div @class([
    "cms-title-block relative overflow-hidden",
    "py-48",
    "bg-center bg-no-repeat bg-cover",
    "animate-fade-in"
]) style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url({{$image}})">
    <!-- Overlay Pattern -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20"></div>
    </div>

    <div class="relative container mx-auto px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-white drop-shadow-2xl leading-tight animate-slide-up">
                <span class="block text-6xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-white via-purple-100 to-pink-100 bg-clip-text text-transparent">
                    {{ $content['title'] }}
                </span>
                @if(isset($content['subtitle']) && $content['subtitle'])
                <span class="block text-xl md:text-2xl font-light text-purple-100 mt-4 opacity-90">
                    {{ $content['subtitle'] }}
                </span>
                @endif
            </h1>

            @if(isset($content['cta_text']) && $content['cta_text'])
            <div class="mt-12 animate-fade-in" style="animation-delay: 0.5s">
                <a href="{{ $content['cta_url'] ?? '#' }}" class="btn-primary inline-flex items-center space-x-3 text-lg px-10 py-4 rounded-full shadow-2xl hover:shadow-purple-500/25">
                    <span>{{ $content['cta_text'] }}</span>
                    <svg class="w-6 h-6 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</div>
@else
<div class="cms-title-block py-32 flex items-center {{ $position === 'left' ? 'flex-row' : 'flex-row-reverse' }} gap-12 animate-fade-in">
    @if(!is_null($image))
    <div class="w-1/2 relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-2xl transform rotate-1 group-hover:rotate-2 transition-transform duration-500 opacity-0 group-hover:opacity-100"></div>
        <div class="relative bg-white p-2 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
            <img src="{{ $image }}" alt="" class="w-full h-auto rounded-xl transition-transform duration-300 group-hover:scale-105">
            <div class="absolute inset-0 rounded-xl bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
    </div>
    @endif
    <div class="w-1/2 {{ $position === 'left' ? 'pl-8' : 'pr-8' }} space-y-8">
        <div class="relative">
            <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-800 via-purple-800 to-pink-800 bg-clip-text text-transparent leading-tight">
                {{ $content['title'] }}
            </h1>
            <div class="absolute -bottom-3 left-0 w-24 h-1 rounded-full" style="background: linear-gradient(135deg, #3991B3 0%, #4da6c7 50%, #5db3d4 100%);"></div>
        </div>

        @if(isset($content['subtitle']) && $content['subtitle'])
        <p class="text-xl text-gray-600 leading-relaxed">
            {{ $content['subtitle'] }}
        </p>
        @endif

        @if(isset($content['cta_text']) && $content['cta_text'])
        <div class="pt-6">
            <a href="{{ $content['cta_url'] ?? '#' }}" class="btn-primary inline-flex items-center space-x-2">
                <span>{{ $content['cta_text'] }}</span>
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        @endif
    </div>
</div>
@endif
