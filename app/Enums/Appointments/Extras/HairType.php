<?php

namespace App\Enums\Appointments\Extras;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum HairType: string implements HasLabel
{
    case Thick = 'thick';
    case Normal = 'normal';
    case Skinny = 'skinny';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('_', ' ')->title()->toString());
    }
}
