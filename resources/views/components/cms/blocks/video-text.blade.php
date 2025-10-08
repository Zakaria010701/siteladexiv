@props(['title' => '', 'content' => '', 'video_url' => '', 'video_position' => 'left', 'aspect_ratio' => '16:9'])

@php
    $videoId = '';
    $videoPlatform = '';

    // Extract video ID from YouTube URLs
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $video_url, $matches)) {
        $videoId = $matches[1];
        $videoPlatform = 'youtube';
    }
    // Extract video ID from Vimeo URLs
    elseif (preg_match('/(?:vimeo\.com\/)(\d+)/', $video_url, $matches)) {
        $videoId = $matches[1];
        $videoPlatform = 'vimeo';
    }

    $aspectClasses = [
        '16:9' => 'aspect-video',
        '4:3' => 'aspect-[4/3]',
        '1:1' => 'aspect-square',
        '21:9' => 'aspect-[21/9]',
    ];

    $aspectClass = $aspectClasses[$aspect_ratio] ?? $aspectClasses['16:9'];
    $isLeft = $video_position === 'left';
@endphp

<section class="cms-block">
    <div class="container mx-auto px-6">
        @if($title)
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
                {{ $title }}
            </h2>
        @endif

        <div class="grid lg:grid-cols-2 gap-8 items-center max-w-7xl mx-auto">
            <!-- Video Section -->
            <div class="{{ $isLeft ? 'lg:order-1' : 'lg:order-2' }} {{ $aspectClass }} bg-black rounded-lg overflow-hidden shadow-lg">
                @if($videoId && $videoPlatform === 'youtube')
                    <iframe
                        src="https://www.youtube.com/embed/{{ $videoId }}"
                        title="{{ $title ?: 'YouTube Video' }}"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                @elseif($videoId && $videoPlatform === 'vimeo')
                    <iframe
                        src="https://player.vimeo.com/video/{{ $videoId }}"
                        title="{{ $title ?: 'Vimeo Video' }}"
                        class="w-full h-full"
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                @else
                    <div class="w-full h-full flex items-center justify-center text-white bg-gray-800">
                        <div class="text-center">
                            <p class="mb-2">⚠️ Video konnte nicht geladen werden</p>
                            <p class="text-sm opacity-75">Bitte prüfen Sie die Video-URL</p>
                            @if($video_url)
                                <p class="text-xs mt-2 opacity-50 break-all">{{ $video_url }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            <div class="{{ $isLeft ? 'lg:order-2' : 'lg:order-1' }} bg-white rounded-lg p-6 shadow-sm">
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! is_array($content) ? implode(' ', $content) : $content !!}
                </div>
            </div>
        </div>
    </div>
</section>