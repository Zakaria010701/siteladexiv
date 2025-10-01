<div class="cms-block">
    @php
        // Get data from the $content variable passed by the CMS builder
        $content = $content ?? [];
        $cardType = $content['card_type'] ?? 'services';
        $cards = $content['cards'] ?? [];
        $title = $content['title'] ?? '';

        // Default cards if none provided
        if (empty($cards)) {
            $cards = [
                [
                    'title' => 'Dauerhafte Haarentfernung',
                    'description' => 'Dank technologischer Fortschritte ist die Laser-Haarentfernung die sicherste Methode. In Frankfurt und Wiesbaden bieten wir professionelle Behandlungen für dauerhafte Ergebnisse an.',
                    'image' => null,
                    'name' => null,
                    'rating' => null,
                    'link' => null,
                ],
                [
                    'title' => 'Tattooentfernung',
                    'description' => 'Die Laserbehandlung ist die beste Methode für eine schonende und narbenfreie Tattooentfernung. In Frankfurt und Wiesbaden bieten wir professionelle Laser-Tattooentfernungen an.',
                    'image' => null,
                    'name' => null,
                    'rating' => null,
                    'link' => null,
                ],
                [
                    'title' => 'Faltenunterspritzung',
                    'description' => 'Möchten Sie ein jugendliches Aussehen ohne Falten im Gesicht oder Dekolleté? Wir bieten in Frankfurt und Wiesbaden Faltenunterspritzungen mit Hyaluron.',
                    'image' => null,
                    'name' => null,
                    'rating' => null,
                    'link' => null,
                ],
                [
                    'title' => 'Ein Schritt in Richtung Selbstvertrauen',
                    'description' => 'Verabschieden Sie sich von unerwünschter Körperbehaarung mit unserer dauerhaften Haarentfernung. Genießen Sie glatte, haarfreie Haut.',
                    'image' => null,
                    'name' => null,
                    'rating' => null,
                    'link' => null,
                ],
            ];
        }

        // Grid layout is handled in the div class directly
    @endphp

    @if($title)
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent mb-4">
                {{ $title }}
            </h2>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($cards as $index => $card)
            @php
                $cardColors = [
                    ['bg-gradient-to-br from-blue-500 to-blue-600', 'group-hover:text-blue-600'],
                    ['bg-gradient-to-br from-emerald-500 to-emerald-600', 'group-hover:text-emerald-600'],
                    ['bg-gradient-to-br from-pink-500 to-pink-600', 'group-hover:text-pink-600'],
                    ['bg-gradient-to-br from-purple-500 to-purple-600', 'group-hover:text-purple-600'],
                    ['bg-gradient-to-br from-orange-500 to-orange-600', 'group-hover:text-orange-600'],
                    ['bg-gradient-to-br from-teal-500 to-teal-600', 'group-hover:text-teal-600'],
                    ['bg-gradient-to-br from-indigo-500 to-indigo-600', 'group-hover:text-indigo-600'],
                    ['bg-gradient-to-br from-red-500 to-red-600', 'group-hover:text-red-600'],
                ];
                $color = $cardColors[$index % count($cardColors)];
            @endphp

            <div class="group h-full">
                @if($cardType === 'testimonials')
                    <!-- Testimonial Card Layout -->
                    <div class="relative p-6 rounded-2xl bg-white/80 backdrop-blur-sm border border-white/20 hover:bg-white/90 hover:border-white/30 transition-all duration-300 hover:shadow-lg">
                        @php
                            $testimonialImagePath = null;
                            $testimonialImageType = $card['image_type'] ?? 'none';

                            if ($testimonialImageType === 'upload' && !empty($card['image'])) {
                                $testimonialImagePath = asset('storage/' . $card['image']);
                            } elseif ($testimonialImageType === 'media' && !empty($card['media_id'])) {
                                // Get media item and its file path
                                $mediaItem = \App\Models\MediaItem::find($card['media_id']);
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
                                     alt="{{ $card['title'] }}"
                                     class="w-20 h-20 rounded-full object-cover mx-auto border-4 border-white shadow-lg"
                                     onerror="this.style.display='none';">
                            </div>
                        @endif

                        <div class="text-center">
                            @if($card['rating'])
                                <div class="flex justify-center mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $card['rating'] ? 'text-yellow-400' : 'text-gray-300' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            @endif

                            <blockquote class="text-gray-600 italic mb-4">
                                "{{ $card['description'] }}"
                            </blockquote>

                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-bold text-gray-800">{{ $card['title'] }}</h4>
                                @if($card['name'])
                                    <p class="text-sm text-gray-500">{{ $card['name'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Service/Feature Card Layout -->
                    <div class="flex items-start space-x-4 p-6 rounded-2xl bg-white/80 backdrop-blur-sm border border-white/20 hover:bg-white/90 hover:border-white/30 transition-all duration-300 hover:shadow-lg h-full">
                        @php
                            $imagePath = null;
                            $imageType = $card['image_type'] ?? 'none';

                            if ($imageType === 'upload' && !empty($card['image'])) {
                                $imagePath = asset('storage/' . $card['image']);
                            } elseif ($imageType === 'media' && !empty($card['media_id'])) {
                                // Get media item and its file path
                                $mediaItem = \App\Models\MediaItem::find($card['media_id']);
                                if ($mediaItem && $mediaItem->mediaFiles()->exists()) {
                                    $spatieMedia = $mediaItem->mediaFiles()->first();
                                    if ($spatieMedia) {
                                        $imagePath = $spatieMedia->getUrl();
                                    }
                                }
                            }
                        @endphp

                        @if($imagePath)
                            <div class="flex-shrink-0">
                                <img src="{{ $imagePath }}"
                                     alt="{{ $card['title'] }}"
                                     class="w-16 h-16 rounded-2xl object-cover shadow-lg"
                                     onerror="console.log('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            </div>
                        @else
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-2xl {{ $color[0] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-gray-800 mb-3 {{ $color[1] }} transition-colors duration-300">
                                {{ $card['title'] }}
                            </h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                {{ $card['description'] }}
                            </p>

                            @if($card['name'])
                                <p class="text-sm font-medium text-gray-700 mb-3">{{ $card['name'] }}</p>
                            @endif

                            @php
                                $linkUrl = '';
                                $linkType = $card['link_type'] ?? 'none';

                                if ($linkType === 'url' && !empty($card['link_url'])) {
                                    $linkUrl = $card['link_url'];
                                } elseif ($linkType === 'cms_page' && !empty($card['link_cms_page'])) {
                                    $cmsPage = \App\Models\CmsPage::find($card['link_cms_page']);
                                    if ($cmsPage) {
                                        $linkUrl = route('cms.page', ['slug' => $cmsPage->slug]);
                                    }
                                }
                            @endphp

                            @if($linkUrl)
                                <a href="{{ $linkUrl }}"
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                    Mehr erfahren
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>