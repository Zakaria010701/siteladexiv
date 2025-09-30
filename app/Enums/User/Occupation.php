<?php

namespace App\Enums\User;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Occupation: string implements HasLabel
{
    case MainJob = 'mainjob';
    case PartTimeJob = 'part-time-job';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }
}
