<?php

namespace App\Enums\Invoices;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum InvoiceType: string implements HasLabel
{
    case Invoice = 'invoice';
    case Proforma = 'proforma';
    case Offer = 'offer';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('_', ' ')->title()->toString());
    }

    public function getSeries(): ?string
    {
        return match ($this) {
            self::Invoice => invoice()->invoice_series,
            self::Proforma => invoice()->proforma_series,
            self::Offer => invoice()->offer_series,
        };
    }
}
