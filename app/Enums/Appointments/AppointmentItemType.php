<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum AppointmentItemType: string implements HasLabel
{
    case Service = 'service';
    case Product = 'product';
    case Contract = 'contract';
    case ConsultationFee = 'consultation-fee';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }
}
