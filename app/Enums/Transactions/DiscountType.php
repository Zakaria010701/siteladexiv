<?php

namespace App\Enums\Transactions;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum DiscountType: string implements HasLabel
{
    case Package = 'package';
    case PackageTemplate = 'package-template';
    case Quantity = 'quantity';
    case Custom = 'custom';
    case Customer = 'customer';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }
}
