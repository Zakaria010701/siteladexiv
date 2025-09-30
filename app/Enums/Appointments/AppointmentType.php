<?php

namespace App\Enums\Appointments;

use App\Models\AppointmentModuleSetting;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum AppointmentType: string implements HasColor, HasLabel
{
    case Treatment = 'treatment';
    case Consultation = 'consultation';
    case TreatmentConsultation = 'treatment-consultation';
    case Debriefing = 'debriefing';
    case FollowUp = 'follow-up';
    case RoomBlock = 'room-block';
    case Reservation = 'reservation';

    public function isConsultation(): bool
    {
        return $this == AppointmentType::Consultation || $this === AppointmentType::TreatmentConsultation;
    }

    public function hasConsultationFee(): bool
    {
        return $this == AppointmentType::Consultation;
    }

    public function isTreatment(): bool
    {
        return $this == AppointmentType::Treatment || $this == AppointmentType::TreatmentConsultation;
    }

    public function isRoomBlock(): bool
    {
        return $this == AppointmentType::RoomBlock;
    }

    public function hasActiveModule(AppointmentModule $module): bool
    {
        $modules = AppointmentModuleSetting::all()
            ->mapWithKeys(fn (AppointmentModuleSetting $item): array => [$item->name->value => $item->appointment_types])
            ->toArray();

        return in_array($this->value, $modules[$module->value]);
    }

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->prepend('appointment.type.')->toString());
    }

    public function getShortCode(): ?string
    {
        return __(Str::of($this->value)->prepend('appointment.type.short.')->toString());
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Treatment => Color::hex('#07c2e4'),
            self::Consultation => Color::hex('#14b4db'),
            self::TreatmentConsultation => Color::hex('#016b98'),
            self::Debriefing => Color::hex('#030174'),
            self::FollowUp => Color::hex('#8a9e99'),
            self::RoomBlock => '#787878',
            default => null,
        };
    }

    public function getOverrideColor()
    {
        return match ($this) {
            self::Treatment,
            self::TreatmentConsultation => false,
            self::Consultation,
            self::Debriefing,
            self::FollowUp,
            self::RoomBlock => true,
            default => null,
        };
    }

    public function requiresCustomer(): bool
    {
        return match ($this) {
            self::Reservation => false,
            self::RoomBlock => false,
            default => true,
        };
    }

    public function getDefaultDuration(): int
    {
        return match ($this) {
            self::Treatment => appointment()->default_treatment_duration,
            self::Consultation => appointment()->default_consultation_duration,
            self::TreatmentConsultation => appointment()->default_treatment_consultation_duration,
            self::Debriefing => appointment()->default_depriefing_duration,
            self::FollowUp => appointment()->default_follow_up_duration,
            default => general()->default_appointment_time,
        };
    }

    public static function getBookingTypes(): array
    {
        return [
            self::Treatment->value => self::Treatment->getLabel(),
            self::Consultation->value => self::Consultation->getLabel(),
            self::TreatmentConsultation->value => self::TreatmentConsultation->getLabel(),
        ];
    }
}
