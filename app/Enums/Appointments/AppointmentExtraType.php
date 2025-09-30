<?php

namespace App\Enums\Appointments;

use App\Enums\Appointments\Extras\HairType;
use App\Enums\Appointments\Extras\PigmentType;
use App\Enums\Appointments\Extras\Satisfaction;
use App\Models\TreatmentType;
use App\Models\TreatmentTypeSpotSizeOption;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

enum AppointmentExtraType: string implements HasLabel
{
    case HairType = 'hair_type';
    case PigmentType = 'pigment_type';
    case Satisfaction = 'satisfaction';
    case SkinType = 'skin_type';
    case Energy = 'energy';
    case LiCount = 'li_count';
    case SpotSize = 'spot_size';
    case Color = 'color';
    case WaveLength = 'wave_length';
    case Milliseconds = 'milliseconds';

    public function options(): ?array
    {
        return match ($this) {
            self::HairType => array_reduce(HairType::cases(), function (array $carry, HairType $type): array {
                $carry[$type->value] = $type->getLabel();

                return $carry;
            }, []),
            self::PigmentType => array_reduce(PigmentType::cases(), function (array $carry, PigmentType $type): array {
                $carry[$type->value] = $type->getLabel();

                return $carry;
            }, []),
            self::Satisfaction => array_reduce(Satisfaction::cases(), function (array $carry, Satisfaction $type): array {
                $carry[$type->value] = $type->getLabel();

                return $carry;
            }, []),
            self::SkinType => [
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
                6 => '6',
            ],
            default => null,
        };
    }

    public function hasDefault(): bool
    {
        return match ($this) {
            self::LiCount, self::Milliseconds, self::WaveLength => true,
            default => false,
        };
    }

    public function canSplitPerService(): bool
    {
        return match ($this) {
            self::Energy, self::SpotSize, self::LiCount, self::Milliseconds, self::WaveLength => true,
            default => false,
        };
    }

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('_', ' ')->title()->toString());
    }

    public static function getSpotSizeOptions(null|int|string|TreatmentType $treatmentType): array
    {
        if ($treatmentType === null) {
            return [];
        }

        if ($treatmentType instanceof TreatmentType) {
            $treatmentType = $treatmentType->id;
        }

        return Cache::rememberForever("treatment_type-$treatmentType-spot_size_options", function () use ($treatmentType) {
            return TreatmentTypeSpotSizeOption::query()
                ->where('treatment_type_id', $treatmentType)
                ->pluck('spot_size', 'spot_size')
                ->toArray();
        });
    }
}
