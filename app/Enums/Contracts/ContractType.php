<?php

namespace App\Enums\Contracts;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ContractType: string implements HasLabel
{
    case VK = 'vk';
    case DV = 'dv';
    case GS = 'gs';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->upper()->toString());
    }
}
