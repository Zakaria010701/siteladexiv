<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Enums\Transactions\PaymentType;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $customer_id
 * @property string $payable_type
 * @property int $payable_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property PaymentType $type
 * @property float $amount
 * @property string $note
 * @property array $meta
 * @property-read Customer|null $customer
 * @property-read Model|\Eloquent $payable
 * @property-read Model|\Eloquent $reference
 *
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment onlyTrashed()
 * @method static Builder|Payment query()
 * @method static Builder|Payment whereAmount($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereCustomerId($value)
 * @method static Builder|Payment whereDeletedAt($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment whereMeta($value)
 * @method static Builder|Payment whereNote($value)
 * @method static Builder|Payment wherePayableId($value)
 * @method static Builder|Payment wherePayableType($value)
 * @method static Builder|Payment whereReferenceId($value)
 * @method static Builder|Payment whereReferenceType($value)
 * @method static Builder|Payment whereType($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @method static Builder|Payment withTrashed()
 * @method static Builder|Payment withoutTrashed()
 *
 * @property-read mixed $badge
 *
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => PaymentType::class,
        'amount' => 'float',
        'meta' => 'array',
    ];

    public function badge(): Attribute
    {
        return Attribute::make(
            get: function () {
                return sprintf('%s %s', $this->type->getLabel(), formatMoney($this->amount));
            }
        );
    }

    public function scopeType(Builder $query, PaymentType $type): void
    {
        $query->where('type', $type->value);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo('payable');
    }

    public function customerCredit() : MorphOne
    {
        return $this->morphOne(CustomerCredit::class, 'source');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }

    public function creditable(): MorphTo
    {
        return $this->morphTo('creditable');
    }

    public function getPayableUrl(): ?string
    {
        if ($this->payable == null) {
            return null;
        }

        if ($this->payable instanceof Appointment) {
            return AppointmentResource::getUrl('edit', ['record' => $this->payable]);
        }

        return null;
    }

    public function getPayableTitle(): ?string
    {
        if ($this->payable == null) {
            return null;
        }

        if ($this->payable instanceof Appointment) {
            return $this->payable->title;
        }

        return null;
    }
}
