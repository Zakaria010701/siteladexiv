<?php

namespace App\Enums\User;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum UserAvailabilityType: string implements HasColor, HasLabel, HasDescription
{
    case Provider = 'provider';
    case Reception = 'reception';
    case Admin = 'admin';
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Provider => "#3788d8",
            self::Admin => "#4CAF50",
            self::Reception => "#f542bc",
        };
    }

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Provider => __('Providers are shown in the calendar and open it for appointments'),
            self::Reception => __('Receptionists are shown in th calendar but do not open it for appointments'),
            self::Admin => __('Admins are not shown in the calendar'),
        };
    }

    public function isAvailabilityHidden(): bool
    {
        return match ($this) {
            self::Admin => true,
            default => false,
        };
    }

    public function isAvailabilityAllDay(): bool
    {
        return match ($this) {
            self::Admin => false,
            default =>  true,
        };
    }
    public function isAvailabilityBackground(): bool
    {
        return match ($this) {
            self::Provider => true,
            default => false,
        };
    }

    public function isAvailabilityBackgroundInverted(): bool
    {
        return match ($this) {
            self::Provider => true,
            default => false,
        };
    }

    public function requiresBoundaryTime(): bool
    {
        return match ($this) {
            self::Admin => false,
            default =>  true,
        };
    }
}
