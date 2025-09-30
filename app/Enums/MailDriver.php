<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum MailDriver: string implements HasDescription, HasLabel
{
    case None = 'none';
    case Smtp = 'smtp';
    case Postmark = 'postmark';
    case Mailgun = 'mailgun';
    case Ses = 'ses';
    case Array = 'array';
    case Log = 'log';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::None => __('Do not use any email driver'),
            self::Postmark => __('Currently not implemented'),
            self::Mailgun => __('Currently not implemented'),
            self::Ses => __('Currently not implemented'),
            default => null,
        };
    }
}
