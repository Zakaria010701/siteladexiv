<div class="cms-slider-block py-12">
    @if($title)
    <h2 class="text-3xl font-bold mb-8 text-center">{{ $title }}</h2>
    @endif

    @php
    $allImages = [];

    // The block data is in the 'content' variable
    $blockData = $content ?? [];
    $mediaIds = $blockData['media_ids'] ?? [];
    $images = $blockData['images'] ?? [];
    $title = $blockData['title'] ?? $title ?? 'No Title';

    if(count($images) > 0) {
        $allImages = $images;
    } elseif(count($mediaIds) > 0) {
        foreach($mediaIds as $mediaId) {
            $mediaItem = \App\Models\MediaItem::find($mediaId);
            if($mediaItem && $mediaItem->mediaFiles->isNotEmpty()) {
                foreach($mediaItem->mediaFiles as $media) {
                    $allImages[] = $media->getUrl();
                }
            }
        }
    }

    // Debug output
    $debugInfo = "Images count: " . count($allImages) . ", Media IDs: " . implode(', ', $mediaIds ?: ['none']) . ", Content keys: " . implode(', ', array_keys($blockData));
    @endphp


    @if(count($allImages) > 0)
    <div class="slider-container relative overflow-hidden rounded-lg shadow-lg">
        <div class="slider-wrapper flex transition-transform duration-500 ease-in-out" id="slider-wrapper-{{ uniqid() }}">
            @foreach($allImages as $image)
            <div class="slider-slide flex-shrink-0 w-full">
                <img src="{{ $image }}" alt="" class="w-full h-80 md:h-128 object-cover">
            </div>
            @endforeach
        </div>

        @if(count($allImages) > 1)
        <!-- Navigation buttons -->
        <button class="slider-prev absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-all" type="button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button class="slider-next absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-all" type="button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Dots indicator -->
        <div class="slider-dots absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            @for($i = 0; $i < count($allImages); $i++)
            <button class="slider-dot w-3 h-3 rounded-full bg-white bg-opacity-50 hover:bg-opacity-75 transition-all {{ $i === 0 ? 'bg-opacity-100' : '' }}" data-slide="{{ $i }}"></button>
            @endfor
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sliderContainer = document.querySelector('.slider-container');
            if (!sliderContainer) return;

            const sliderWrapper = sliderContainer.querySelector('.slider-wrapper');
            const slides = sliderContainer.querySelectorAll('.slider-slide');
            const prevBtn = sliderContainer.querySelector('.slider-prev');
            const nextBtn = sliderContainer.querySelector('.slider-next');
            const dots = sliderContainer.querySelectorAll('.slider-dot');

            if (slides.length <= 1) return;

            let currentSlide = 0;
            const totalSlides = {{ count($allImages) }};
            let autoplayInterval = null;

            function updateSlider() {
                sliderWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;

                // Update dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('bg-opacity-100', index === currentSlide);
                    dot.classList.toggle('bg-opacity-50', index !== currentSlide);
                });
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }

            function prevSlide() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlider();
            }

            function goToSlide(slideIndex) {
                currentSlide = slideIndex;
                updateSlider();
            }

            // Event listeners
            if (nextBtn) nextBtn.addEventListener('click', nextSlide);
            if (prevBtn) prevBtn.addEventListener('click', prevSlide);

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => goToSlide(index));
            });

            // Autoplay
            @if($autoplay)
            function startAutoplay() {
                autoplayInterval = setInterval(nextSlide, {{ $autoplayDelay }});
            }

            function stopAutoplay() {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval);
                    autoplayInterval = null;
                }
            }

            sliderContainer.addEventListener('mouseenter', stopAutoplay);
            sliderContainer.addEventListener('mouseleave', startAutoplay);

            startAutoplay();
            @endif

            // Initialize
            updateSlider();
        });
    </script>
    @endif
</div>