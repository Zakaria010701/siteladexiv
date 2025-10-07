<?php

namespace App\View\Components\Cms\Blocks;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class ImageText extends Component
{
    public ?string $image;
    public string $imagePosition;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $content
    )
    {
        // Handle both direct image upload and media gallery selection
        if (isset($content['image']) && !empty($content['image'])) {
            // Direct image upload - handle array or string
            if (is_array($content['image'])) {
                // Filament often stores as array with path as first element
                $imagePath = is_array($content['image']) ? ($content['image'][0] ?? reset($content['image'])) : $content['image'];
            } else {
                $imagePath = $content['image'];
            }

            // Image processing completed successfully

            // Try both Storage::url() and asset() to see which works
            $storageUrl = Storage::url($imagePath);
            $assetUrl = asset('storage/' . $imagePath);

            // Use asset() as fallback since Storage::url() might not work with current server setup
            $this->image = $assetUrl;

            // Using asset() for better Laravel server compatibility
        } elseif (isset($content['media_id']) && !empty($content['media_id'])) {
            // Media gallery selection
            $mediaItem = \App\Models\MediaItem::find($content['media_id']);
            $media = $mediaItem && $mediaItem->mediaFiles->isNotEmpty() ? $mediaItem->mediaFiles->first() : null;
            $this->image = $media ? $media->getUrl() : null;

            // Media gallery image processed successfully
        } else {
            $this->image = null;
        }

        $this->imagePosition = $content['image_position'] ?? 'left';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cms.blocks.image-text');
    }
}