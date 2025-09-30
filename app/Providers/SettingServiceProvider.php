<?php

namespace App\Providers;

use App\Settings\IntegrationSettings;
use App\Settings\NotificationSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->bootNotificationSettings();
        $this->bootIntegrationSettings();
    }

    private function bootNotificationSettings(): void
    {
        // Check that the settings table exists
        if (! Schema::hasTable('settings')) {
            return;
        }

        // Do not use notification settings in local env by default
        if (App::environment(['local', 'staging', 'dev'])) {
            return;
        }

        $settings = app(NotificationSetting::class);

        try {
            config([
                'mail.default' => $settings->enable_failover ? $settings->default_mailer->value : 'failover',
                'mail.mailers.smtp.host' => $settings->smtp_host,
                'mail.mailers.smtp.port' => $settings->smtp_port,
                'mail.mailers.smtp.encription' => $settings->smtp_encription,
                'mail.mailers.smtp.username' => $settings->smtp_username,
                'mail.mailers.smtp.password' => Crypt::decrypt($settings->smtp_password),

                'mail.from' => [
                    'address' => $settings->from_email,
                    'name' => $settings->from_name,
                ],

                'services.sms77.api_key' => $settings->sms_77_api_key,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function bootIntegrationSettings(): void
    {
        // Check that the settings table exists
        if (! Schema::hasTable('settings')) {
            return;
        }

        // Do not use notification settings in local env by default
        if (App::environment(['local', 'staging', 'dev'])) {
            return;
        }

        $settings = app(IntegrationSettings::class);

        try {
            config([
                'google-calendar.calendar_id' => $settings->google_calendar_id,
                'google-calendar.service_account.credentials_json' => $settings->google_credentials_json_path,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
