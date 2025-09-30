<?php

namespace App\Forms\Components;

use Filament\Schemas\Components\Section;
use Closure;
use Filament\Forms\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class CustomSection extends Section
{
    protected string $view = 'forms.components.custom-section';

    public static function make(string | array | Htmlable | Closure | null $heading = null): static
    {
        return parent::make($heading);
    }
}
