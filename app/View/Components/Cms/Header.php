<?php

namespace App\View\Components\Cms;

use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Header extends Component
{
    public Collection $items;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->items = CmsMenuItem::query()->with('childItems')->whereNull('parent_id')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cms.header');
    }
}
