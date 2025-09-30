<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('frontend', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('booking_constraint_by_category', true);
            $blueprint->add('booking_constraint_by_services', true);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('frontend', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('booking_constraint_by_category');
            $blueprint->delete('booking_constraint_by_services');
        });
    }
};
