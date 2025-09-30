<?php

namespace App\Support\Appointment;

use App\Models\Appointment;

class AppointmentFormSupport
{
    public function __construct(private readonly ?Appointment $appointment) {}
}
