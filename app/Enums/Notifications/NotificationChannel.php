<?php

namespace App\Enums\Notifications;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum NotificationChannel: string implements HasLabel
{
    case Mail = 'mail';
    case Sms = 'sms';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }
}
