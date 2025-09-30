<?php

use App\Enums\MailDriver;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('notification', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('disable_notifications', false);
            $blueprint->add('send_email_notification_by_default', false);
            $blueprint->add('send_sms_notification_by_default', false);
            $blueprint->add('email_header');
            $blueprint->add('email_footer');
            $blueprint->add('from_name', '');
            $blueprint->add('from_email', '');
            $blueprint->add('default_mailer', MailDriver::Smtp);

            $blueprint->add('smtp_host', null);
            $blueprint->add('smtp_port', null);
            $blueprint->add('smtp_encription', null);
            $blueprint->add('smtp_username', null);
            $blueprint->add('smtp_password', null);

            $blueprint->add('enable_failover', false);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('notification', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('disable_notifications');
            $blueprint->delete('send_email_notification_by_default');
            $blueprint->delete('send_sms_notification_by_default');
            $blueprint->delete('email_header');
            $blueprint->delete('email_footer');
            $blueprint->delete('from_name');
            $blueprint->delete('from_email');
            $blueprint->delete('default_mailer');
            $blueprint->delete('smtp_host');
            $blueprint->delete('smtp_port');
            $blueprint->delete('smtp_encription');
            $blueprint->delete('smtp_username');
            $blueprint->delete('smtp_password');
            $blueprint->delete('enable_failover');
        });
    }
};
