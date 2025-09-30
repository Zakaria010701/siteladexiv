<?php

namespace App\Forms\Components\TableRepeater\Concerns;

use Filament\Support\Enums\Width;
use Closure;

trait HasBreakPoints
{
    protected string | Width | Closure | null $stackAt = null;

    public function stackAt(string | Width | Closure $stackAt): static
    {
        $this->stackAt = $stackAt;

        return $this;
    }

    public function getStackAt(): string | Width
    {
        return $this->evaluate($this->stackAt)
            ?? Width::Medium;
    }
}
