<?php

namespace App\View\Components\Cms\Blocks;

use Closure;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RichText extends Component
{
    public string $text;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $content,
    )
    {
        $this->text = $content['content'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cms.blocks.rich-text');
    }
}
