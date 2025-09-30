<?php

namespace App\Enums\Appointments\Extras;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PigmentType: string implements HasLabel
{
    case None = 'none';
    case Pale = 'pale';
    case Medium = 'medium';
    case Strong = 'strong';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('_', ' ')->title()->toString());
    }
}
