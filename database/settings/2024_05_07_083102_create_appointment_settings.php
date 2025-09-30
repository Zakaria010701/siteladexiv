<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('appointment', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('min_appointment_duration', 15);
            $blueprint->add('max_appointment_duration', 300);
            $blueprint->add('default_treatment_duration', 30);
            $blueprint->add('default_consultation_duration', 30);
            $blueprint->add('default_treatment_consultation_duration', 30);
            $blueprint->add('default_depriefing_duration', 30);
            $blueprint->add('default_follow_up_duration', 30);
        });
    }
};
