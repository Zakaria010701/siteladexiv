<?php

namespace App\Models;

use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Contracts\ContractType;
use App\Models\Concerns\CanBeVerified;
use App\Observers\ContractObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(ContractObserver::class)]
/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $customer_id
 * @property int|null $appointment_id
 * @property int|null $credited_appointment_id
 * @property int|null $payment_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon $date
 * @property ContractType $type
 * @property string|null $description
 * @property string $default_price
 * @property string $discount_percentage
 * @property string $sub_total
 * @property string $price
 * @property int $treatment_count
 * @property array<array-key, mixed>|null $meta
 * @property-read \App\Models\Appointment|null $appointment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractService> $contractServices
 * @property-read int|null $contract_services_count
 * @property-read \App\Models\Appointment|null $creditedAppointment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $creditedPayments
 * @property-read int|null $credited_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceCredit> $credits
 * @property-read int|null $credits_count
 * @property-read \App\Models\Verification|null $currentVerification
 * @property-read \App\Models\Customer|null $customer
 * @property-read mixed $label
 * @property-read \App\Models\Payment|null $payment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @property-read mixed $title
 * @property-read mixed $treatments_open
 * @property-read mixed $treatments_used
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Verification> $verifications
 * @property-read int|null $verifications_count
 * @method static Builder<static>|Contract newModelQuery()
 * @method static Builder<static>|Contract newQuery()
 * @method static Builder<static>|Contract notPaid()
 * @method static Builder<static>|Contract notVerified()
 * @method static Builder<static>|Contract onlyTrashed()
 * @method static Builder<static>|Contract paid()
 * @method static Builder<static>|Contract query()
 * @method static Builder<static>|Contract unused()
 * @method static Builder<static>|Contract used()
 * @method static Builder<static>|Contract verified()
 * @method static Builder<static>|Contract whereAppointmentId($value)
 * @method static Builder<static>|Contract whereCreatedAt($value)
 * @method static Builder<static>|Contract whereCreditedAppointmentId($value)
 * @method static Builder<static>|Contract whereCustomerId($value)
 * @method static Builder<static>|Contract whereDate($value)
 * @method static Builder<static>|Contract whereDefaultPrice($value)
 * @method static Builder<static>|Contract whereDeletedAt($value)
 * @method static Builder<static>|Contract whereDescription($value)
 * @method static Builder<static>|Contract whereDiscountPercentage($value)
 * @method static Builder<static>|Contract whereId($value)
 * @method static Builder<static>|Contract whereMeta($value)
 * @method static Builder<static>|Contract wherePaymentId($value)
 * @method static Builder<static>|Contract wherePrice($value)
 * @method static Builder<static>|Contract whereSubTotal($value)
 * @method static Builder<static>|Contract whereTreatmentCount($value)
 * @method static Builder<static>|Contract whereType($value)
 * @method static Builder<static>|Contract whereUpdatedAt($value)
 * @method static Builder<static>|Contract whereUserId($value)
 * @method static Builder<static>|Contract withTrashed()
 * @method static Builder<static>|Contract withoutTrashed()
 * @mixin \Eloquent
 */
class Contract extends Model
{
    use SoftDeletes;
    use CanBeVerified;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'type' => ContractType::class,
        'meta' => 'array',
    ];

    /*------------------------------
    |   Attributes
    --------------------------------*/

    public function title(): Attribute
    {
        return Attribute::make(
            get: function () {
                return sprintf('%s %s (%s)',
                    formatDate($this->date),
                    $this->type->getLabel(),
                    $this->treatment_count,
                );
            }
        );
    }

    public function label(): Attribute
    {
        return Attribute::make(
            get: function () {
                return sprintf('%s %s %s',
                    $this->contractServices->pluck('badge')->join(','),
                    $this->type->getLabel(),
                    formatDate($this->date),
                );
            }
        );
    }

    public function treatmentsUsed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->credits()->selectRaw('count(id) as used')->whereNotNull('used_at')->groupBy('service_id')->pluck('used')->min() ?? 0
        );
    }

    public function treatmentsOpen(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->treatment_count - $this->treatments_used
        );
    }

    /*------------------------------
    |   Scopes
    --------------------------------*/

    public function scopePaid(Builder $query): void
    {
        $query->whereHas('appointment', fn (Appointment|Builder $query) => $query->paid());
    }

    public function scopeNotPaid(Builder $query): void
    {
        $query->whereHas('appointment', fn (Appointment|Builder $query) => $query->orderStatus(AppointmentOrderStatus::Open, '='));
    }

    public function scopeUsed(Builder $query): void
    {
        $query->whereDoesntHave('credits', fn(ServiceCredit|Builder $query) => $query->unused());
    }
    public function scopeUnused(Builder $query): void
    {
        $query->whereHas('credits', fn(ServiceCredit|Builder $query) => $query->unused());
    }

    /*------------------------------
    |   Methods
    --------------------------------*/

    public function isPaid(): bool
    {
        if (isset($this->appointment)) {
            return $this->appointment->isPaid();
        }

        return false;
    }

    public function isUsed(): bool
    {
        return $this->credits->reject(fn (ServiceCredit $credit) => $credit->isUsed())->isEmpty();
    }

    /*------------------------------
    |   Relationships
    --------------------------------*/

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function creditedAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'credited_appointment_id');
    }

    public function credits(): HasMany
    {
        return $this->hasMany(ServiceCredit::class);
    }

    public function contractServices(): HasMany
    {
        return $this->hasMany(ContractService::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function creditedPayments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'creditable');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'contract_services');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
