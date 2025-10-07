<div style="width: 100%; position: relative; margin: 0; padding: 0;">
    @php
        $height = $content['height'] ?? '400px';
        $imageUrl = '';
        $imageAlt = $content['title'] ?? '';

        // Determine image source
        if($content['image'] ?? false) {
            $imageUrl = asset('storage/' . $content['image']);
        } elseif($content['media_id'] ?? false) {
            $mediaItem = \App\Models\MediaItem::find($content['media_id']);
            $media = $mediaItem && $mediaItem->mediaFiles->isNotEmpty() ? $mediaItem->mediaFiles->first() : null;
            if($media) {
                $imageUrl = $media->getUrl();
                $imageAlt = $media->name;
            }
        }
    @endphp

    <div style="position: relative; width: 100%; height: {{ $height }}; overflow: hidden; background-color: #f0f0f0;">
        @if($imageUrl)
        <img
            src="{{ $imageUrl }}"
            alt="{{ $imageAlt }}"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; display: block;"
            onload="this.style.opacity = 1;"
            onerror="this.style.display='none';"
        >
        @endif

        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 2;">
            <div style="text-align: center; color: white; padding: 20px;">
                <h1 style="font-size: clamp(2rem, 5vw, 4rem); font-weight: bold; margin: 0; color: #3991B3; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); line-height: 1.2;">
                    {{ $content['title'] }}
                </h1>
            </div>
        </div>
    </div>


</div>