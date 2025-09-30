<?php

namespace App\Enums\TimeRecords;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum LeaveType: string implements HasColor, HasLabel
{
    case Vacation = 'vacation';
    case SickLeave = 'sick-leave';
    case School = 'school';
    case UnpaidLeave = 'unpaid-leave';
    case OvertimeReduction = 'overtime-reduction';
    case Holiday = 'holiday';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Vacation => Color::Green,
            self::SickLeave => Color::Amber,
            self::UnpaidLeave => Color::Red,
            default => Color::Sky,
        };
    }

    public function getShortCode(): string
    {
        return match ($this) {
            self::Vacation => 'U',
            self::SickLeave => 'K',
            self::UnpaidLeave => 'Z',
            self::OvertimeReduction => 'A',
            self::School => 'S',
            self::Holiday => 'F',
        };
    }

    /**
     * Determines if the vacation type adds the full target to the actual in the time report.
     */
    public function getTargetFulfilled(): bool
    {
        return match ($this) {
            self::Vacation => true,
            self::SickLeave => true,
            self::UnpaidLeave => true,
            self::OvertimeReduction => false,
            self::School => false,
            self::Holiday => true,
        };
    }
}
