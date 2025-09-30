<?php

use App\Enums\TimeStep;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('frontend', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('customer_login_enabled', true);

            $blueprint->add('appointment_cancelation_enabled', true);
            $blueprint->add('appointment_cancelation_before_time', 1);
            $blueprint->add('appointment_cancelation_before_step', TimeStep::Days->value);

            $blueprint->add('appointment_reschedule_enabled', true);
            $blueprint->add('appointment_reschedule_before_time', 1);
            $blueprint->add('appointment_reschedule_before_step', TimeStep::Days->value);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('frontend', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('customer_login_enabled');

            $blueprint->delete('appointment_cancelation_enabled');
            $blueprint->delete('appointment_cancelation_before_time');
            $blueprint->delete('appointment_cancelation_before_step');

            $blueprint->delete('appointment_reschedule_enabled');
            $blueprint->delete('appointment_reschedule_before_time');
            $blueprint->delete('appointment_reschedule_before_step');
        });
    }
};
