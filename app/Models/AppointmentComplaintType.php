<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property-read Collection<int, AppointmentComplaint> $appointmentComplaints
 * @property-read int|null $appointment_complaints_count
 * @method static Builder<static>|AppointmentComplaintType newModelQuery()
 * @method static Builder<static>|AppointmentComplaintType newQuery()
 * @method static Builder<static>|AppointmentComplaintType query()
 * @method static Builder<static>|AppointmentComplaintType whereCreatedAt($value)
 * @method static Builder<static>|AppointmentComplaintType whereId($value)
 * @method static Builder<static>|AppointmentComplaintType whereName($value)
 * @method static Builder<static>|AppointmentComplaintType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AppointmentComplaintType extends Model
{
    protected $guarded = ['id'];

    public function appointmentComplaints(): HasMany
    {
        return $this->hasMany(AppointmentComplaint::class);
    }
}
