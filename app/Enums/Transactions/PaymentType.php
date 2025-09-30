<?php

namespace App\Enums\Transactions;

use App\Models\CustomerCredit;
use App\Models\Invoice;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PaymentType: string implements HasLabel
{
    case Cash = 'cash';
    case Card = 'card';
    case Contract = 'contract';
    case Credit = 'credit';
    case Debit = 'debit';
    case Goodwill = 'goodwill';
    case Invoice = 'invoice';
    case PayPal = 'pay-pal';
    case PriceChange = 'price-change';
    case Proforma = 'proforma';
    case Split = 'split';
    case Transaction = 'transaction';
    //case Voucher = 'voucher';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getReferenceType(): ?string
    {
        return match ($this) {
            self::Credit => CustomerCredit::class,
            self::Invoice => Invoice::class,
            default => null
        };
    }
}
