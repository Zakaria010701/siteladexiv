<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationSettings extends Settings
{
    public bool $google_sync_calendar;
    public ?string $google_calendar_id;
    public ?string $google_credentials_json_path;

    public static function group(): string
    {
        return 'integration';
    }
}
