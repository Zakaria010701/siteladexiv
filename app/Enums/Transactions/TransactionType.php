<?php

namespace App\Enums\Transactions;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TransactionType: string implements HasLabel, HasColor
{
    case Withdrawal = 'withdrawal';
    case Deposit = 'deposit';
    case Transfer = 'transfer';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Withdrawal => 'warning',
            self::Deposit => 'success',
            self::Transfer => 'info',
        };
    }
}
