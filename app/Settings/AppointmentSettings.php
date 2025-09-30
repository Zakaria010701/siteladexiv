<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppointmentSettings extends Settings
{
    public int $min_appointment_duration;

    public int $max_appointment_duration;

    public int $default_treatment_duration;

    public int $default_consultation_duration;

    public int $default_treatment_consultation_duration;

    public int $default_depriefing_duration;

    public int $default_follow_up_duration;

    public bool $consultation_fee_enabled;

    public float $consultation_fee;

    public bool $consultation_fee_credits_enabled;

    public static function group(): string
    {
        return 'appointment';
    }
}
