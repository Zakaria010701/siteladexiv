<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Weekday: int implements HasLabel
{
    case Sunday = 0;
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;

    public function getLabel(): ?string
    {
        return __(Str::of($this->name)->replace('-', ' ')->title()->toString());
    }

    public function toCarbonWeekDay(): \Carbon\WeekDay
    {
        return match ($this) {
            self::Sunday => \Carbon\WeekDay::Sunday,
            self::Monday => \Carbon\WeekDay::Monday,
            self::Tuesday => \Carbon\WeekDay::Tuesday,
            self::Wednesday => \Carbon\WeekDay::Wednesday,
            self::Thursday => \Carbon\WeekDay::Thursday,
            self::Friday => \Carbon\WeekDay::Friday,
            self::Saturday => \Carbon\WeekDay::Saturday,
        };
    }
}
