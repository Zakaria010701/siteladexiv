<?php

namespace App\Enums\Appointments;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum AppointmentModule: string implements HasLabel
{
    case Participants = 'participants';
    case Order = 'order';
    case Status = 'status';
    case Extras = 'extras';
    case Consultation = 'consultation';
    case Done = 'done';
    case Notes = 'notes';
    case History = 'history';
    case ServiceDetails = 'service-details';
    case Complaint = 'complaint';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->append(' Module')->replace('-', ' ')->title()->toString());
    }
}
