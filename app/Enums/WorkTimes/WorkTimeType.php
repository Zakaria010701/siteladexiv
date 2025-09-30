<?php

namespace App\Enums\WorkTimes;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum WorkTimeType: string implements HasColor, HasLabel
{
    case Provider = 'provider';
    case Reception = 'reception';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Reception => Color::generateV3Palette('#f542bc'),
            default => Color::generateV3Palette('#3788d8'),
        };
    }

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }
}
