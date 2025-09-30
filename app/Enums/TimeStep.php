<?php

namespace App\Enums;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TimeStep: string implements HasLabel
{
    case None = 'none';
    case Days = 'days';
    case Weeks = 'weeks';
    case Months = 'months';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function add(CarbonInterface $date, int $value): CarbonInterface
    {
        return match ($this) {
            self::Days => $date->addDays($value),
            self::Weeks => $date->addWeeks($value),
            self::Months => $date->addMonths($value),
            default => $date,
        };
    }

    public function diff(CarbonInterface $date, CarbonInterface $compare): int
    {
        return match ($this) {
            self::Days => $date->diffInDays($compare),
            self::Weeks => $date->diffInWeeks($compare),
            self::Months => $date->diffInMonths($compare),
            default => 0,
        };
    }

    public function getInterval(int $value): ?CarbonInterval
    {
        return match ($this) {
            self::Days => CarbonInterval::days($value),
            self::Weeks => CarbonInterval::weeks($value),
            self::Months => CarbonInterval::months($value),
            default => null,
        };
    }
}
