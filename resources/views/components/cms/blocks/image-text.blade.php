<div class="cms-block py-8">
    <div class="flex {{ trim($content['image_position'] ?? 'left') === 'left' ? 'flex-row image-left' : 'flex-row-reverse image-right' }} items-start gap-6">
        @if($content['image'] ?? false)
        <div class="flex-1 {{ trim($content['image_position'] ?? 'left') === 'left' ? 'pr-6' : 'pl-6' }}">
            <img src="{{ asset('storage/' . $content['image']) }}" alt="" class="w-full h-auto rounded-lg object-contain max-w-full">
        </div>
        @elseif($content['media_id'] ?? false)
        @php
            $mediaItem = \App\Models\MediaItem::find($content['media_id']);
            $media = $mediaItem && $mediaItem->mediaFiles->isNotEmpty() ? $mediaItem->mediaFiles->first() : null;
        @endphp
        @if($media)
        <div class="flex-1 {{ trim($content['image_position'] ?? 'left') === 'left' ? 'pr-6' : 'pl-6' }}">
            <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="w-full h-auto rounded-lg object-contain max-w-full">
        </div>
        @endif
        @endif
        <div class="flex-1 {{ trim($content['image_position'] ?? 'left') === 'left' ? 'pl-6' : 'pr-6' }}">
            @if(isset($content['title']) && $content['title'])
            <h2 class="text-3xl font-bold mb-4">{{ $content['title'] }}</h2>
            @endif
            <div class="prose prose-lg max-w-none">
                {!! $content['content'] ?? '' !!}
            </div>
        </div>
    </div>
</div>