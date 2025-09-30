<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TimeSettings extends Settings
{
    public int $two_hour_break;

    public int $four_hour_break;

    public int $six_hour_break;

    public int $eight_hour_break;

    public int $ten_hour_break;

    public int $early_check_in_minutes;

    public int $late_check_in_minutes;

    public int $early_check_out_minutes;

    public int $late_check_out_minutes;

    public bool $worktime_prevent_check_in_before;

    public int $worktime_prevent_check_in_before_minutes;

    public bool $worktime_auto_logout_users;

    public int $worktime_auto_logout_after_minutes;

    public bool $target_auto_logout_users;

    public int $target_auto_logout_after_minutes;

    public bool $overtime_cap_enabled;

    public int $overtime_cap_minutes;

    public static function group(): string
    {
        return 'time';
    }
}
