<?php

namespace App\Enums\TimeRecords;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TimeCheckStatus: string implements HasColor, HasLabel
{
    case Ok = 'ok';
    case Early = 'early';
    case Late = 'late';
    case Automatic = 'automatic';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Ok => 'success',
            self::Early => 'warning',
            self::Late => 'danger',
            default => Color::Sky,
        };
    }
}
