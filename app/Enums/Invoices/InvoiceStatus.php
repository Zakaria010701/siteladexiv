<?php

namespace App\Enums\Invoices;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum InvoiceStatus: string implements HasColor, HasLabel, HasIcon
{
    case Open = 'open';
    case Paid = 'paid';
    case Canceled = 'canceled';
    case DebtCollection = 'debt_collection';

    case Reminder = 'reminder';

    public function getLabel(): ?string
    {
        return __('invoice.status.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Open => 'primary',
            self::Paid, self::DebtCollection => 'success',
            self::Canceled => 'gray',
            self::Reminder => 'warning',
        };
    }

    public function getIcon(): \BackedEnum|string|null
    {
        return match ($this) {
            self::Open => Heroicon::QuestionMarkCircle,
            self::Paid, self::DebtCollection => Heroicon::CheckCircle,
            self::Canceled => Heroicon::XCircle,
            self::Reminder => Heroicon::ExclamationCircle,
        };
    }

    public function isOpen(): bool
    {
        return match ($this) {
            self::Paid, self::Canceled, self::DebtCollection => false,
            default => true
        };
    }
}
