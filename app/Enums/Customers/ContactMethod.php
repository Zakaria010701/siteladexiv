<?php

namespace App\Enums\Customers;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ContactMethod: string implements HasLabel
{
    case Email = 'email';
    case Phone = 'phone';
    case Sms = 'sms';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('_', ' ')->title()->toString());
    }
}
