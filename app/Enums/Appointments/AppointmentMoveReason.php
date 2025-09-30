<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasLabel;

enum AppointmentMoveReason: string implements HasLabel
{
    case Optimization = 'optimization';
    case EmployeeAbsence = 'employee_absence';
    case CustomerRequest = 'customer_request';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return __("appointment.movement_reason.$this->value");
    }
}
