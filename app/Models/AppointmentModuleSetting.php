<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Appointments\AppointmentModule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AppointmentModuleSetting
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property AppointmentModule $name
 * @property array $appointment_types
 *
 * @method static Builder|AppointmentModuleSetting newModelQuery()
 * @method static Builder|AppointmentModuleSetting newQuery()
 * @method static Builder|AppointmentModuleSetting query()
 * @method static Builder|AppointmentModuleSetting whereAppointmentTypes($value)
 * @method static Builder|AppointmentModuleSetting whereCreatedAt($value)
 * @method static Builder|AppointmentModuleSetting whereId($value)
 * @method static Builder|AppointmentModuleSetting whereName($value)
 * @method static Builder|AppointmentModuleSetting whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AppointmentModuleSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'name' => AppointmentModule::class,
        'appointment_types' => 'array',
    ];
}
