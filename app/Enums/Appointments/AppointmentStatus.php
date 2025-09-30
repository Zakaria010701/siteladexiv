<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Done = 'done';
    case Canceled = 'canceled';

    public function isPending(): bool
    {
        return $this == AppointmentStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this == AppointmentStatus::Approved;
    }

    public function isDone(): bool
    {
        return $this == AppointmentStatus::Done;
    }

    public function isCanceled(): bool
    {
        return $this == AppointmentStatus::Canceled;
    }

    public function getLabel(): ?string
    {
        return __('status.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Approved => 'primary',
            self::Done => 'success',
            self::Canceled => 'danger',
        };
    }
}
