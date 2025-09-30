<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasLabel;

enum CancelReason: string implements HasLabel
{
    case SameDayCancellation = 'same-day-cancellation';
    case CustomerSick = 'customer-sick';
    case CustomerNotAppeared = 'customer-not-appeared';
    case Other = 'other';

    public function isConsultation(): bool
    {
        return $this == AppointmentType::Consultation;
    }

    public function getLabel(): ?string
    {
        return __("appointment.cancel_reason.$this->value");
    }
}
