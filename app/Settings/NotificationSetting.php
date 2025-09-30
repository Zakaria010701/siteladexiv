<?php

namespace App\Settings;

use App\Enums\MailDriver;
use Spatie\LaravelSettings\Settings;

class NotificationSetting extends Settings
{
    public bool $disable_notifications;

    public bool $send_email_notification_by_default;

    public bool $send_sms_notification_by_default;

    public ?array $email_header;

    public ?array $email_footer;

    public string $from_name;

    public string $from_email;

    public MailDriver $default_mailer;

    public ?string $smtp_host;

    public ?string $smtp_port;

    public ?string $smtp_encription;

    public ?string $smtp_username;

    public ?string $smtp_password;

    public bool $enable_failover;

    public ?string $sms_77_api_key;

    public ?string $sms_77_from;

    public static function group(): string
    {
        return 'notification';
    }
}
