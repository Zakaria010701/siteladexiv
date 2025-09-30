<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('calendar', function (\Spatie\LaravelSettings\Migrations\SettingsBlueprint $blueprint): void {
            $blueprint->add('group_resources_in_day_plan', true);
            $blueprint->add('group_users_in_day_plan', false);

            $blueprint->add('slot_duration', "00:15");
            $blueprint->add('slot_label_interval', "00:30");

            $blueprint->add('now_indicator', true);
            $blueprint->add('dates_above_resources', true);
        });
    }
};
