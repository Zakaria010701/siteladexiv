<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('general', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('date_format', 'Y.m.d');
            $blueprint->add('time_format', 'H:i');
            $blueprint->add('default_time_slot', 15);
            $blueprint->add('default_appointment_time', 30);
        });
    }
};
