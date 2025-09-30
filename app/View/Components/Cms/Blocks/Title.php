<?php

namespace App\View\Components\Cms\Blocks;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class Title extends Component
{
    public ?string $image;
    public string $position;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $content
    )
    {
        $this->image = isset($content['image']) ? Storage::url($content['image']): null;
        $this->position = $content['position'] ?? 'center';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cms.blocks.title');
    }
}
