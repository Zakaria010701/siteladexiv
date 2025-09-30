<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Appointments\AppointmentExtraType;
use App\Enums\Appointments\Extras\HairType;
use App\Enums\Appointments\Extras\PigmentType;
use App\Enums\Appointments\Extras\Satisfaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AppointmentDetail
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $appointment_id
 * @property HairType|null $hair_type
 * @property PigmentType|null $pigment_type
 * @property int|null $skin_type
 * @property Satisfaction|null $satisfaction
 * @property float|null $energy
 * @property int|null $li_count
 * @property int|null $spot_size
 * @property int|null $wave_length
 * @property int|null $milliseconds
 * @property-read Appointment|null $appointment
 *
 * @method static Builder|AppointmentDetail newModelQuery()
 * @method static Builder|AppointmentDetail newQuery()
 * @method static Builder|AppointmentDetail query()
 * @method static Builder|AppointmentDetail whereAppointmentId($value)
 * @method static Builder|AppointmentDetail whereCreatedAt($value)
 * @method static Builder|AppointmentDetail whereEnergy($value)
 * @method static Builder|AppointmentDetail whereHairType($value)
 * @method static Builder|AppointmentDetail whereId($value)
 * @method static Builder|AppointmentDetail whereLiCount($value)
 * @method static Builder|AppointmentDetail whereMilliseconds($value)
 * @method static Builder|AppointmentDetail wherePigmentType($value)
 * @method static Builder|AppointmentDetail whereSatisfaction($value)
 * @method static Builder|AppointmentDetail whereSkinType($value)
 * @method static Builder|AppointmentDetail whereSpotSize($value)
 * @method static Builder|AppointmentDetail whereUpdatedAt($value)
 * @method static Builder|AppointmentDetail whereWaveLength($value)
 *
 * @mixin \Eloquent
 */
class AppointmentDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'hair_type' => HairType::class,
        'pigment_type' => PigmentType::class,
        'satisfaction' => Satisfaction::class,
        'meta' => 'array',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function getExtraValue(AppointmentExtraType $type): mixed
    {
        return match ($type) {
            AppointmentExtraType::HairType => $this->hair_type?->value,
            AppointmentExtraType::PigmentType => $this->pigment_type?->value,
            AppointmentExtraType::SkinType => $this->skin_type,
            AppointmentExtraType::Satisfaction => $this->satisfaction?->value,
            AppointmentExtraType::Energy => $this->energy,
            AppointmentExtraType::LiCount => $this->li_count,
            AppointmentExtraType::SpotSize => $this->spot_size,
            AppointmentExtraType::WaveLength => $this->wave_length,
            AppointmentExtraType::Milliseconds => $this->milliseconds,
            default => null,
        };
    }
}
