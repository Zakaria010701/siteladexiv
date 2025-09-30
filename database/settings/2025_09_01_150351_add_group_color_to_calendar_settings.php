<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('calendar', function (\Spatie\LaravelSettings\Migrations\SettingsBlueprint $blueprint): void {
            $blueprint->add('resource_group_color', "#E36E0C");
            $blueprint->add('user_group_color', "#3788d8");
        });
    }
};
