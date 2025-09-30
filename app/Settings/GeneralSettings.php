<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $date_format;

    public string $time_format;

    public int $default_time_slot;

    public int $default_appointment_time;

    public static function group(): string
    {
        return 'general';
    }
}
