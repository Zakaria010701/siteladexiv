<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('company', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('logo_path', '');
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('company', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('logo_path');
        });
    }
};
