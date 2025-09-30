<?php

namespace App\Enums\Appointments\Extras;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Satisfaction: int implements HasLabel
{
    case Excellent = 1;
    case Good = 2;
    case Satisfactory = 3;
    case Sufficient = 4;
    case Inadequate = 5;
    case Insufficient = 6;

    public function getLabel(): ?string
    {
        return __(Str::of($this->name)->replace('_', ' ')->title()->toString());
    }
}
