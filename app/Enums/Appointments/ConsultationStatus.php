<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ConsultationStatus: string implements HasColor, HasLabel
{
    case Success = 'success';
    case WillCall = 'will_call';
    case Considering = 'considering';
    case NeedsRecall = 'needs_recall';
    case PriceToHigh = 'price_to_high';
    case Failure = 'failure';
    case TreatmentImpossible = 'treatment_impossible';

    public function isSuccess(): bool
    {
        return $this == self::Success || $this == self::WillCall;
    }

    public function isFailure(): bool
    {
        return $this == self::Failure || $this == self::TreatmentImpossible;
    }

    public function getLabel(): ?string
    {
        return __('status.consultation.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Success => 'success',
            self::WillCall => 'success',
            self::Considering => 'warning',
            self::NeedsRecall => 'warning',
            self::PriceToHigh => 'warning',
            self::Failure => 'danger',
            self::TreatmentImpossible => 'grey',
        };
    }
}
