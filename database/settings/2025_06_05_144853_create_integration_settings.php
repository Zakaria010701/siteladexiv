<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('integration', function (\Spatie\LaravelSettings\Migrations\SettingsBlueprint $blueprint): void {
            $blueprint->add('google_calendar_id', null);
            $blueprint->add('google_credentials_json_path', '');
        });
    }
};
