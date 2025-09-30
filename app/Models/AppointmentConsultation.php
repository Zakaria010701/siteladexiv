<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Appointments\ConsultationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AppointmentConsultation
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $appointment_id
 * @property int $customer_id
 * @property ConsultationStatus $status
 * @property bool $informed_about_risks
 * @property bool $has_special_risks
 * @property string|null $special_risks
 * @property bool $takes_medicine
 * @property string|null $medicine
 * @property bool $individual_responsibility_signed
 * @property bool $informed_about_consultation_fee
 * @property-read Appointment|null $appointment
 * @property-read Customer|null $customer
 *
 * @method static Builder|AppointmentConsultation newModelQuery()
 * @method static Builder|AppointmentConsultation newQuery()
 * @method static Builder|AppointmentConsultation query()
 * @method static Builder|AppointmentConsultation whereAppointmentId($value)
 * @method static Builder|AppointmentConsultation whereCreatedAt($value)
 * @method static Builder|AppointmentConsultation whereCustomerId($value)
 * @method static Builder|AppointmentConsultation whereHasSpecialRisks($value)
 * @method static Builder|AppointmentConsultation whereId($value)
 * @method static Builder|AppointmentConsultation whereIndividualResponsibilitySigned($value)
 * @method static Builder|AppointmentConsultation whereInformedAboutConsultationFee($value)
 * @method static Builder|AppointmentConsultation whereInformedAboutRisks($value)
 * @method static Builder|AppointmentConsultation whereMedicine($value)
 * @method static Builder|AppointmentConsultation whereSpecialRisks($value)
 * @method static Builder|AppointmentConsultation whereStatus($value)
 * @method static Builder|AppointmentConsultation whereTakesMedicine($value)
 * @method static Builder|AppointmentConsultation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AppointmentConsultation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ConsultationStatus::class,
        'informed_about_risks' => 'boolean',
        'has_special_risks' => 'boolean',
        'takes_medicine' => 'boolean',
        'individual_responsibility_signed' => 'boolean',
        'informed_about_consultation_fee' => 'boolean',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
