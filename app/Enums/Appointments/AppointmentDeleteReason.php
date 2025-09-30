<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasLabel;

enum AppointmentDeleteReason: string implements HasLabel
{
    case CustomerRequest = 'customer_request';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return __("appointment.delete_reason.$this->value");
    }
}
