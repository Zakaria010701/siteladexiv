<?php

namespace App\View\Components\Cms\Blocks;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class Slider extends Component
{
    public array $images;
    public ?string $title;
    public bool $autoplay;
    public int $autoplayDelay;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $content
    )
    {
        $this->title = $content['title'] ?? null;
        $this->autoplay = $content['autoplay'] ?? false;
        $this->autoplayDelay = $content['autoplay_delay'] ?? 3000;

        // Process images
        $this->images = [];
        if (isset($content['images']) && is_array($content['images'])) {
            foreach ($content['images'] as $image) {
                $this->images[] = Storage::url($image);
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cms.blocks.slider');
    }
}