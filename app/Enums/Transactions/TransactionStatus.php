<?php

namespace App\Enums\Transactions;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TransactionStatus: string implements HasLabel, HasColor
{
    case Booked = 'booked';
    case Open = 'open';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Open => 'warning',
            self::Booked => 'success',
        };
    }
}
