<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Enums\Appointments\AppointmentOrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AppointmentOrder
 *
 * @property AppointmentOrderStatus $status
 * @property-read Appointment|null $appointment
 *
 * @method static Builder|AppointmentOrder newModelQuery()
 * @method static Builder|AppointmentOrder newQuery()
 * @method static Builder|AppointmentOrder query()
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $appointment_id
 * @property float $base_total
 * @property float $discount_total
 * @property float $net_total
 * @property float $tax_total
 * @property float $gross_total
 * @property float $paid_total
 * @property array|null $meta
 *
 * @method static Builder|AppointmentOrder whereAppointmentId($value)
 * @method static Builder|AppointmentOrder whereBaseTotal($value)
 * @method static Builder|AppointmentOrder whereCreatedAt($value)
 * @method static Builder|AppointmentOrder whereDiscountTotal($value)
 * @method static Builder|AppointmentOrder whereGrossTotal($value)
 * @method static Builder|AppointmentOrder whereId($value)
 * @method static Builder|AppointmentOrder whereMeta($value)
 * @method static Builder|AppointmentOrder whereNetTotal($value)
 * @method static Builder|AppointmentOrder wherePaidTotal($value)
 * @method static Builder|AppointmentOrder whereStatus($value)
 * @method static Builder|AppointmentOrder whereTaxTotal($value)
 * @method static Builder|AppointmentOrder whereUpdatedAt($value)
 * @method static Builder|AppointmentOrder status(AppointmentOrderStatus $status, string $operator = '=')
 *
 * @mixin \Eloquent
 */
class AppointmentOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => AppointmentOrderStatus::class,
        'base_total' => 'float',
        'discount_total' => 'float',
        'net_total' => 'float',
        'tax_total' => 'float',
        'gross_total' => 'float',
        'paid_total' => 'float',
        'meta' => 'array',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function scopeStatus(Builder $query, AppointmentOrderStatus $status, string $operator = '='): void
    {
        $query->where('status', $operator, $status->value);
    }
}
