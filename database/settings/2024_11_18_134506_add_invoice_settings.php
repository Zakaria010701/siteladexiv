<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('invoice', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('invoice_series', 'RE');
            $blueprint->add('proforma_series', 'PF');
            $blueprint->add('offer_series', 'OF');

            $blueprint->add('due_after_days', 14);

            $blueprint->add('default_header', []);
            $blueprint->add('default_footer', []);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('invoice', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('invoice_series');
            $blueprint->delete('proforma_series');
            $blueprint->delete('offer_series');

            $blueprint->delete('due_after_days');

            $blueprint->delete('default_header');
            $blueprint->delete('default_footer');
        });
    }
};
