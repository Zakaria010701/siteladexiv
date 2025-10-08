<section class="cms-block">
    <div class="container mx-auto px-6">
        @php
        $allImages = [];
        $blockData = $content ?? [];
        $title = $blockData['title'] ?? '';
        $content = $blockData['content'] ?? '';
        $sliderPosition = $blockData['slider_position'] ?? 'left';
        $autoplay = $blockData['autoplay'] ?? false;
        $autoplayDelay = $blockData['autoplay_delay'] ?? 3000;
        $titleColor = $blockData['title_color'] ?? '#000000';

        $mediaIds = $blockData['media_ids'] ?? [];
        $images = $blockData['images'] ?? [];

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
        @endphp

        @if($title)
            <h2 class="text-3xl font-bold mb-12 text-center" style="color: {{ $titleColor }};">
                {{ $title }}
            </h2>
        @endif

            @if(count($allImages) > 0)
                <div class="grid lg:grid-cols-2 gap-8 items-center max-w-7xl mx-auto">
                    <!-- Slider Section -->
                    <div class="{{ $sliderPosition === 'left' ? 'lg:order-1' : 'lg:order-2' }}">
                        <div class="slider-container relative overflow-hidden rounded-lg shadow-lg">
                            <div class="slider-wrapper flex transition-transform duration-500 ease-in-out" id="slider-wrapper-{{ uniqid() }}">
                                @foreach($allImages as $image)
                                <div class="slider-slide flex-shrink-0 w-full">
                                    <img src="{{ $image }}" alt="" class="w-full h-64 md:h-80 lg:h-96 object-cover">
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
                    </div>
   
                    <!-- Content Section -->
                    <div class="{{ $sliderPosition === 'left' ? 'lg:order-2' : 'lg:order-1' }} bg-white rounded-lg p-6 shadow-sm">
                        @if($content)
                            <div class="prose prose-lg max-w-none text-gray-700">
                                {!! $content !!}
                            </div>
                        @else
                            <div class="text-gray-500 italic">
                                Kein Textinhalt hinzugefügt.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

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
            @if($autoplay ?? false)
            function startAutoplay() {
                autoplayInterval = setInterval(nextSlide, {{ $autoplayDelay ?? 3000 }});
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
</div>