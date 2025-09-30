<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('invoice', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('default_tax', 19);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('invoice', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('default_tax');
        });
    }
};
