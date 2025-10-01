
@php
    // Get data from the $content variable passed by the CMS builder
    $content = $content ?? [];
    $testimonials = $content['testimonials'] ?? [];
    $title = $content['title'] ?? '';

    // Background customization options
    $backgroundType = $content['background_type'] ?? 'transparent';
    $backgroundColor = $content['background_color'] ?? '#ffffff';
    $gradientType = $content['gradient_type'] ?? 'primary';

    // Card customization options
    $cardBackgroundColor = $content['card_background_color'] ?? '#ffffff';
    $cardBackgroundOpacity = $content['card_background_opacity'] ?? '80'; // 80% opacity

    // Ensure background color is always valid
    if (empty($backgroundColor) || !preg_match('/^#[0-9A-Fa-f]{6}$/', $backgroundColor)) {
        $backgroundColor = '#ffffff';
    }

    // Ensure card background color is always valid
    if (empty($cardBackgroundColor) || !preg_match('/^#[0-9A-Fa-f]{6}$/', $cardBackgroundColor)) {
        $cardBackgroundColor = '#ffffff';
    }

    // Ensure opacity is valid (0-100)
    if (!is_numeric($cardBackgroundOpacity) || $cardBackgroundOpacity < 0 || $cardBackgroundOpacity > 100) {
        $cardBackgroundOpacity = 80;
    }
    $paddingTop = $content['padding_top'] ?? 8;
    $paddingBottom = $content['padding_bottom'] ?? 8;
    $marginTop = $content['margin_top'] ?? 0;
    $marginBottom = $content['margin_bottom'] ?? 0;
    $borderStyle = $content['border_style'] ?? 'none';
    $borderColor = $content['border_color'] ?? '#3991B3';
    $cornerRadius = $content['corner_radius'] ?? 'lg';
    $shadowStyle = $content['shadow_style'] ?? 'md';

    // Define gradient styles
    $gradients = [
        'primary' => 'linear-gradient(135deg, #3991B3 0%, #4da6c7 50%, #5db3d4 100%)',
        'medical' => 'linear-gradient(135deg, #3991B3 0%, #ffffff 50%, #3991B3 100%)',
        'warm' => 'linear-gradient(135deg, #f97316 0%, #ec4899 50%, #8b5cf6 100%)',
        'cool' => 'linear-gradient(135deg, #14b8a6 0%, #3b82f6 50%, #6366f1 100%)',
        'rainbow' => 'linear-gradient(135deg, #ff0000 0%, #ff7f00 14%, #ffff00 28%, #00ff00 42%, #0000ff 57%, #4b0082 71%, #9400d3 85%, #ff0000 100%)',
    ];

    // Define shadow styles
    $shadows = [
        'none' => '',
        'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        'xl' => '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
    ];

    // Define corner radius styles
    $corners = [
        'none' => '0',
        'sm' => '0.25rem',
        'md' => '0.5rem',
        'lg' => '0.75rem',
        'xl' => '1rem',
        'full' => '9999px',
    ];

    // Build background style
    $backgroundStyle = '';
    if ($backgroundType === 'solid') {
        $backgroundStyle = "background-color: {$backgroundColor};";
    } elseif ($backgroundType === 'gradient') {
        $backgroundStyle = "background: {$gradients[$gradientType]};";
    } elseif ($backgroundType === 'glass') {
        $backgroundStyle = "background: rgba({$backgroundColor}, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);";
    } elseif ($backgroundType === 'transparent' && !empty($backgroundColor)) {
        // Apply background color even for transparent type if color is specified
        $backgroundStyle = "background-color: {$backgroundColor};";
    }

    // Build border style
    $borderStyleValue = '';
    if ($borderStyle === 'solid') {
        $borderStyleValue = "border: 2px solid {$borderColor};";
    } elseif ($borderStyle === 'dashed') {
        $borderStyleValue = "border: 2px dashed {$borderColor};";
    } elseif ($borderStyle === 'gradient') {
        $borderStyleValue = "border: 2px solid; border-image: {$gradients[$gradientType]} 1;";
    }

    // Build shadow style
    $shadowStyleValue = '';
    if ($shadowStyle !== 'none') {
        $shadowStyleValue = "box-shadow: {$shadows[$shadowStyle]};";
    }

    // Build corner radius style
    $cornerStyle = '';
    if ($cornerRadius !== 'none') {
        $cornerStyle = "border-radius: {$corners[$cornerRadius]};";
    }

    // Build spacing styles
    $spacingStyle = "padding-top: {$paddingTop}rem; padding-bottom: {$paddingBottom}rem; margin-top: {$marginTop}rem; margin-bottom: {$marginBottom}rem;";
@endphp

<div class="testimonial-cards-container {{ $backgroundType === 'gradient' ? 'gradient' : '' }} {{ $backgroundType === 'glass' ? 'glass' : '' }} {{ $backgroundType === 'solid' ? 'solid' : '' }}"
     style="{{ $spacingStyle }} {{ $backgroundStyle }} {{ $borderStyleValue }} {{ $shadowStyleValue }} {{ $cornerStyle }}">
@php
    // Default testimonials if none provided
        if (empty($testimonials)) {
            $testimonials = [
                [
                    'name' => 'Anna Müller',
                    'position' => 'Kundin seit 2023',
                    'testimonial' => 'Die Laser-Haarentfernung hat mein Leben verändert! Endlich keine tägliche Rasur mehr und die Haut fühlt sich super glatt an. Das Team ist sehr professionell und die Behandlung war schmerzlos.',
                    'rating' => '5',
                    'image' => null,
                ],
                [
                    'name' => 'Maria Santos',
                    'position' => 'Tattoo-Entfernung',
                    'testimonial' => 'Nach jahrelangem Bedauern über mein Tattoo habe ich endlich den Mut gefasst, es entfernen zu lassen. Das Ergebnis ist fantastisch! Die Laserbehandlung war viel schonender als erwartet.',
                    'rating' => '5',
                    'image' => null,
                ],
                [
                    'name' => 'Jennifer Weber',
                    'position' => 'Faltenbehandlung',
                    'testimonial' => 'Die Faltenunterspritzung hat Wunder gewirkt! Ich sehe Jahre jünger aus und fühle mich viel selbstbewusster. Die Beratung war excellent und das Ergebnis natürlich.',
                    'rating' => '5',
                    'image' => null,
                ],
                [
                    'name' => 'Sarah Kim',
                    'position' => 'Stammkundin',
                    'testimonial' => 'Seit ich die dauerhafte Haarentfernung entdeckt habe, bin ich süchtig danach! Alle Mitarbeiter sind super freundlich und die Praxis ist sehr modern eingerichtet.',
                    'rating' => '5',
                    'image' => null,
                ],
            ];
        }
    @endphp

    @if($title)
        <div class="text-center mb-12 relative z-10">
            <h2 class="text-3xl font-bold text-white mb-4">
                {{ $title }}
            </h2>
        </div>
    @else
        <div class="text-center mb-12 relative z-10">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent mb-4">
                Was unsere Kunden sagen
            </h2>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
        @foreach($testimonials as $testimonial)
            <div class="group">
                <div class="relative p-6 rounded-2xl border border-white/20 transition-all duration-300 hover:shadow-lg h-full text-white"
                     style="background-color: {{ $cardBackgroundColor }}{{ dechex((int)((100 - (int)$cardBackgroundOpacity) * 2.55)) }}; backdrop-filter: blur(10px); {{ (int)$cardBackgroundOpacity < 100 ? 'border: 1px solid rgba(255, 255, 255, 0.1);' : '' }}"
                     onmouseover="this.style.backgroundColor='{{ $cardBackgroundColor }}{{ dechex((int)((100 - min((int)$cardBackgroundOpacity + 10, 100)) * 2.55)) }}'"
                     onmouseout="this.style.backgroundColor='{{ $cardBackgroundColor }}{{ dechex((int)((100 - (int)$cardBackgroundOpacity) * 2.55)) }}'">
                    @php
                        $testimonialImagePath = null;
                        $testimonialImageType = $testimonial['image_type'] ?? 'none';

                        if ($testimonialImageType === 'upload' && !empty($testimonial['image'])) {
                            $testimonialImagePath = asset('storage/' . $testimonial['image']);
                        } elseif ($testimonialImageType === 'media' && !empty($testimonial['media_id'])) {
                            // Get media item and its file path
                            $mediaItem = \App\Models\MediaItem::find($testimonial['media_id']);
                            if ($mediaItem && $mediaItem->mediaFiles()->exists()) {
                                $spatieMedia = $mediaItem->mediaFiles()->first();
                                if ($spatieMedia) {
                                    $testimonialImagePath = $spatieMedia->getUrl();
                                }
                            }
                        }
                    @endphp

                    @if($testimonialImagePath)
                        <div class="mb-4">
                            <img src="{{ $testimonialImagePath }}"
                                 alt="{{ $testimonial['name'] }}"
                                 class="w-20 h-20 rounded-full object-cover mx-auto border-4 border-white shadow-lg"
                                 onerror="this.style.display='none';">
                        </div>
                    @endif

                    <div class="text-center flex-1">
                        @if($testimonial['rating'])
                            <div class="flex justify-center mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"
                                              fill="{{ $i <= $testimonial['rating'] ? '#fbbf24' : '#d1d5db' }}"/>
                                    </svg>
                                @endfor
                            </div>
                        @endif

                        <blockquote class="text-white italic mb-6 text-sm leading-relaxed">
                            "{{ $testimonial['testimonial'] }}"
                        </blockquote>

                        <div class="border-t border-gray-300/50 pt-4 mt-auto">
                            <h4 class="font-bold text-white text-lg">{{ $testimonial['name'] }}</h4>
                            @if($testimonial['position'])
                                <p class="text-sm text-gray-200">{{ $testimonial['position'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>