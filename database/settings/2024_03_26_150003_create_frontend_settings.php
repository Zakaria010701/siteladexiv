<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('frontend', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('slot_step', 15);
            $blueprint->add('min_duration', 30);
            $blueprint->add('max_duration', 60);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('frontend', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('slot_step');
            $blueprint->delete('min_duration');
            $blueprint->delete('max_duration');
        });
    }
};
