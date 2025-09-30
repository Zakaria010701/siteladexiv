<?php

namespace App\Enums\Todos;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TodoPriority: int implements HasLabel
{
    case Low = 0;
    case Medium = 10;
    case High = 20;
    case Urgent = 30;

    public function getLabel(): ?string
    {
        return __(Str::of($this->name)->replace('-', ' ')->title()->toString());
    }
}
