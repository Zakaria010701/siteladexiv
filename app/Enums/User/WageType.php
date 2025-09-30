<?php

namespace App\Enums\User;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum WageType: string implements HasLabel
{
    case Hourly = 'hourly';
    case Weekly = 'weekly';
    case Monthly = 'monthly';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->append(' wage')->title()->toString());
    }
}
