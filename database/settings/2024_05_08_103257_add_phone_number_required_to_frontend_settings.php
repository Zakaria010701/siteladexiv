<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('frontend', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('email_required', true);
            $blueprint->add('phone_number_required', false);
        });
    }
};
