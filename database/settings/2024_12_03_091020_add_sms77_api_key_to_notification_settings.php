<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('notification', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('sms_77_api_key', null);
            $blueprint->add('sms_77_from', null);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('notification', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('sms_77_api_key');
            $blueprint->delete('sms_77_from');
        });
    }
};
