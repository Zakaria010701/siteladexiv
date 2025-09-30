<?php

namespace App\Models\Concerns\Appointments;

use App\Models\AppointmentComplaint;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasComplaint
{
    public function complaint(): HasOne
    {
        return $this->hasOne(AppointmentComplaint::class);
    }
}
