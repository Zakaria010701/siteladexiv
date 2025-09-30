<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Gender: string implements HasLabel
{
    case Female = 'female';
    case Male = 'male';
    case NonBinary = 'non-binary';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }
}
