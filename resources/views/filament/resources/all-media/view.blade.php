<div style="padding: 1rem; max-width: 600px; margin: 0 auto; background: white;">
    <h2 style="color: #1f2937; font-size: 1.25rem; margin-bottom: 1rem; text-align: center;">
        {{ $record->name }}
    </h2>

    @php
        $mediaFiles = $record->mediaFiles ?? collect();
    @endphp

    @if($mediaFiles->isNotEmpty())
        @foreach($mediaFiles as $media)
            @if(str_starts_with($media->mime_type, 'image/'))
                <div style="text-align: center; margin-bottom: 1rem;">
                    <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    @if($media->name)
                        <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: #6b7280;">{{ $media->name }}</p>
                    @endif
                </div>
            @endif
        @endforeach
    @else
        <p style="text-align: center; color: #ef4444; padding: 2rem;">No images found</p>
    @endif

    <div style="text-align: center; margin-top: 2rem;">
        <button onclick="window.history.back()" style="background: #6b7280; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">← Back</button>
    </div>
</div>