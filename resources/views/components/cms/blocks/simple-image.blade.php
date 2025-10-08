@props(['images' => [], 'alt_text' => '', 'alignment' => 'center', 'size' => 'full'])

@php
    // Handle both single image (legacy) and multiple images
    $imageArray = is_array($images) ? $images : (empty($images) ? [] : [$images]);

    $alignmentClasses = [
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end',
    ];

    $sizeClasses = [
        'small' => 'w-full max-w-sm',
        'medium' => 'w-full max-w-md',
        'large' => 'w-full max-w-lg',
        'full' => 'w-full',
    ];

    $gridClasses = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4',
    ];

    $alignmentClass = $alignmentClasses[$alignment] ?? $alignmentClasses['center'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['full'];
    $gridClass = $gridClasses[count($imageArray)] ?? $gridClasses[1];
@endphp

<section class="cms-block py-8">
    <div class="container mx-auto px-6">
        @if(count($imageArray) > 0)
            <div class="flex {{ $alignmentClass }}">
                <div class="w-full">
                    <div class="grid {{ $gridClass }} gap-4">
                        @foreach($imageArray as $index => $image)
                            @php
                                $imageUrl = $image ? '/storage/' . $image : '';
                                $imageAlt = is_array($alt_text) && isset($alt_text[$index])
                                    ? $alt_text[$index]
                                    : ($alt_text ?: 'Bild ' . ($index + 1));
                            @endphp
                            @if($imageUrl)
                                <div class="{{ $sizeClass }}">
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $imageAlt }}"
                                        class="w-full h-auto rounded-lg shadow-lg transition-transform duration-300 hover:scale-105"
                                        loading="lazy"
                                    />
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>