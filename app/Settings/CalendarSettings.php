<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CalendarSettings extends Settings
{

    public bool $group_resources_in_day_plan;
    public bool $group_users_in_day_plan;
    public ?string $resource_group_color;
    public ?string $user_group_color;

    public string $slot_duration;
    public string $slot_label_interval;

    public bool $now_indicator;
    public bool $dates_above_resources;

    public static function group(): string
    {
        return 'calendar';
    }
}
