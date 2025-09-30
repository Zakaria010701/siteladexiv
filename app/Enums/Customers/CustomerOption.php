<?php

namespace App\Enums\Customers;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum CustomerOption: string implements HasLabel
{
    case NoNotifications = 'no_notifications';
    case NoEmails = 'no_emails';
    case NoNewsletters = 'no_newsletters';
    case NoPhoneCalls = 'no_phone_calls';
    case NoSms = 'no_sms';
    case AllowsPictureUsage = 'allows_picture_usage';
    case NoFurtherAppointments = 'no_further_appointments';
    case IsVip = 'is_vip';
    case IsDifficult = 'is_difficult';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('_', ' ')->title()->toString());
    }
}
