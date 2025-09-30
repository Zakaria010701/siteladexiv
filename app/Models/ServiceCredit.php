<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\CustomerCredits\CustomerCreditResource;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ServiceCredit
 *
 * @property-read Customer|null $customer
 * @property-read Customer|null $service
 * @property-read Model|Eloquent|null $source
 * @property-read Model|Eloquent|null $usage
 *
 * @method static Builder|ServiceCredit newModelQuery()
 * @method static Builder|ServiceCredit newQuery()
 * @method static Builder|ServiceCredit query()
 * @method static Builder|ServiceCredit unused()
 * @method static Builder|ServiceCredit used()
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $customer_id
 * @property int $service_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property string|null $usage_type
 * @property int|null $usage_id
 * @property string $price
 * @property Carbon|null $used_at
 *
 * @method static Builder|ServiceCredit whereCreatedAt($value)
 * @method static Builder|ServiceCredit whereCustomerId($value)
 * @method static Builder|ServiceCredit whereId($value)
 * @method static Builder|ServiceCredit wherePrice($value)
 * @method static Builder|ServiceCredit whereServiceId($value)
 * @method static Builder|ServiceCredit whereSourceId($value)
 * @method static Builder|ServiceCredit whereSourceType($value)
 * @method static Builder|ServiceCredit whereUpdatedAt($value)
 * @method static Builder|ServiceCredit whereUsageId($value)
 * @method static Builder|ServiceCredit whereUsageType($value)
 * @method static Builder|ServiceCredit whereUsedAt($value)
 *
 * @mixin Eloquent
 */
class ServiceCredit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /*------------------------------
    |   Relationships
    --------------------------------*/

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function contractService(): BelongsTo
    {
        return $this->belongsTo(ContractService::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo('source');
    }

    public function usage(): MorphTo
    {
        return $this->morphTo('usage');
    }

    /*------------------------------
    |   Scopes
    --------------------------------*/

    public function scopeUsed(Builder $query)
    {
        $query->whereNotNull('used_at');
    }

    public function scopeUnused(Builder $query)
    {
        $query->whereNull('used_at');
    }

    /*------------------------------
    |   Methods
    --------------------------------*/

    public function isUsed(): bool
    {
        return ! is_null($this->used_at);
    }

    public function getSourceUrl(): ?string
    {
        if ($this->source == null) {
            return null;
        }

        if ($this->source instanceof Appointment) {
            return AppointmentResource::getUrl('edit', ['record' => $this->source]);
        }

        if ($this->source instanceof AppointmentItem) {
            $record = $this->source->appointment;

            return isset($record) ? AppointmentResource::getUrl('edit', ['record' => $record]) : '';
        }

        return null;
    }

    public function getSourceTitle(): ?string
    {
        if ($this->source == null) {
            return null;
        }

        if ($this->source instanceof Appointment) {
            return $this->source->title;
        }

        if ($this->source instanceof AppointmentItem) {
            $start = $this->source->appointment?->start;

            return isset($start) ? formatDateTime($start) : '';
        }

        return null;
    }

    public function getUsageUrl(): ?string
    {
        if ($this->usage == null) {
            return null;
        }

        if ($this->usage instanceof Appointment) {
            return AppointmentResource::getUrl('edit', ['record' => $this->usage]);
        }

        if ($this->usage instanceof CustomerCredit) {
            return CustomerCreditResource::getUrl('edit', ['record' => $this->usage]);
        }

        if ($this->usage instanceof AppointmentItem) {
            $record = $this->usage->appointment;

            return isset($record) ? AppointmentResource::getUrl('edit', ['record' => $record]) : '';
        }

        return null;
    }

    public function getUsageTitle(): ?string
    {
        if ($this->usage == null) {
            return null;
        }

        if ($this->usage instanceof Appointment) {
            return $this->usage->title;
        }

        if ($this->usage instanceof CustomerCredit) {
            return __('Credit :amount', ['amount' => $this->usage->amount]);
        }

        if ($this->usage instanceof AppointmentItem) {
            $start = $this->usage->appointment?->start;

            return isset($start) ? formatDateTime($start) : '';
        }

        return null;
    }
}
