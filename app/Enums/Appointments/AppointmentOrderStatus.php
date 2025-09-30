<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentOrderStatus: string implements HasColor, HasLabel
{
    case Open = 'open';
    case Paid = 'paid';
    case Canceled = 'canceled';

    public function isOpen(): bool
    {
        return $this == AppointmentOrderStatus::Open;
    }

    public function isPaid(): bool
    {
        return $this == AppointmentOrderStatus::Paid;
    }

    public function isCanceled(): bool
    {
        return $this == AppointmentOrderStatus::Canceled;
    }

    public function getLabel(): ?string
    {
        return __('status.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Open => 'gray',
            self::Paid => 'success',
            self::Canceled => 'danger',
        };
    }
}
