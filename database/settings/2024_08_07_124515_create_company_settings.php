<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('company', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('name', '');
            $blueprint->add('phone', '');
            $blueprint->add('email', '');
            $blueprint->add('location', '');
            $blueprint->add('postcode', '');
            $blueprint->add('address', '');
            $blueprint->add('vat_id', '');
            $blueprint->add('tax_id', '');
            $blueprint->add('bank_name', '');
            $blueprint->add('bank_iban', '');
            $blueprint->add('bank_bic', '');
            $blueprint->add('website', '');
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('company', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('name');
            $blueprint->delete('phone');
            $blueprint->delete('email');
            $blueprint->delete('location');
            $blueprint->delete('postcode');
            $blueprint->delete('address');
            $blueprint->delete('vat_id');
            $blueprint->delete('tax_id');
            $blueprint->delete('bank_name');
            $blueprint->delete('bank_iban');
            $blueprint->delete('bank_bic');
            $blueprint->delete('website');
        });
    }
};
