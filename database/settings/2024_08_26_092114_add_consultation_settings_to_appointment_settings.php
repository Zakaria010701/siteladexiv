<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('appointment', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('consultation_fee_enabled', true);
            $blueprint->add('consultation_fee', 30);
            $blueprint->add('consultation_fee_credits_enabled', true);
        });
    }
};
