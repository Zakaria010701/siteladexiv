<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Appointments\AppointmentExtraType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\AppointmentExtra
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property AppointmentExtraType $type
 * @property string|null $default
 * @property int $is_required
 * @property int $take_from_last_appointment
 * @property array $appointment_types
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 *
 * @method static Builder|AppointmentExtra newModelQuery()
 * @method static Builder|AppointmentExtra newQuery()
 * @method static Builder|AppointmentExtra query()
 * @method static Builder|AppointmentExtra whereAppointmentTypes($value)
 * @method static Builder|AppointmentExtra whereCreatedAt($value)
 * @method static Builder|AppointmentExtra whereDefault($value)
 * @method static Builder|AppointmentExtra whereId($value)
 * @method static Builder|AppointmentExtra whereIsRequired($value)
 * @method static Builder|AppointmentExtra whereTakeFromLastAppointment($value)
 * @method static Builder|AppointmentExtra whereType($value)
 * @method static Builder|AppointmentExtra whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AppointmentExtra extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => AppointmentExtraType::class,
        'appointment_types' => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function treatmentTypes(): BelongsToMany
    {
        return $this->belongsToMany(TreatmentType::class)->withTimestamps();
    }
}
