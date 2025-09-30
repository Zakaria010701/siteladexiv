<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum SmtpEncryption: string implements HasLabel
{
    case TLS = 'tls';
    case SSL = 'ssl';
    case Disabled = 'disabled';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }
}
