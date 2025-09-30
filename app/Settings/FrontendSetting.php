<?php

namespace App\Settings;

use App\Enums\TimeStep;
use Spatie\LaravelSettings\Settings;

class FrontendSetting extends Settings
{
    public int $slot_step;

    public int $min_duration;

    public int $max_duration;

    public bool $customer_login_enabled;

    public bool $appointment_cancelation_enabled;

    public int $appointment_cancelation_before_time;

    public TimeStep $appointment_cancelation_before_step;

    /** @var bool Allow customers to reschedule their appointments */
    public bool $appointment_reschedule_enabled;

    public int $appointment_reschedule_before_time;

    public TimeStep $appointment_reschedule_before_step;

    /** @var bool Only allow booking for users that provide the selected category */
    public bool $booking_constraint_by_category;

    /** @var bool Only allow booking for users that provide the selected services */
    public bool $booking_constraint_by_services;

    public bool $email_required;

    public bool $phone_number_required;

    public static function group(): string
    {
        return 'frontend';
    }
}
