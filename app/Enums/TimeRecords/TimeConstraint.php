<?php

namespace App\Enums\TimeRecords;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TimeConstraint: string implements HasColor, HasLabel
{
    case Worktime = 'worktime';
    case Target = 'target-time';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Worktime => Color::Blue,
            self::Target => Color::Green,
        };
    }

    public function getAutologout(): bool
    {
        return match ($this) {
            self::Worktime => timeSettings()->worktime_auto_logout_users,
            self::Target => timeSettings()->target_auto_logout_users,
        };
    }

    public function getAutologoutAfterMinutes(): int
    {
        return match ($this) {
            self::Worktime => timeSettings()->worktime_auto_logout_after_minutes,
            self::Target => timeSettings()->target_auto_logout_after_minutes,
        };
    }
}
