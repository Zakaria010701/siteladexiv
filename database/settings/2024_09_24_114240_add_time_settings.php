<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('time', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('two_hour_break', 10);
            $blueprint->add('four_hour_break', 20);
            $blueprint->add('six_hour_break', 30);
            $blueprint->add('eight_hour_break', 30);
            $blueprint->add('ten_hour_break', 30);

            $blueprint->add('early_check_in_minutes', 0);
            $blueprint->add('late_check_in_minutes', 0);

            $blueprint->add('early_check_out_minutes', 0);
            $blueprint->add('late_check_out_minutes', 0);

            $blueprint->add('worktime_prevent_check_in_before', true);
            $blueprint->add('worktime_prevent_check_in_before_minutes', 0);

            $blueprint->add('worktime_auto_logout_users', true);
            $blueprint->add('worktime_auto_logout_after_minutes', 0);

            $blueprint->add('target_auto_logout_users', true);
            $blueprint->add('target_auto_logout_after_minutes', 0);

            $blueprint->add('overtime_cap_enabled', true);
            $blueprint->add('overtime_cap_minutes', 10);
        });
    }
};
