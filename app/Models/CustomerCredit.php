<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Transactions\PaymentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $customer_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property string $amount
 * @property string|null $description
 * @property Carbon|null $spent_at
 * @property array|null $meta
 * @property-read Customer $customer
 * @property-read bool $is_spent
 * @property-read float $open_amount
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read Model|\Eloquent $source
 * @property-read float $used_amount
 *
 * @method static Builder|CustomerCredit newModelQuery()
 * @method static Builder|CustomerCredit newQuery()
 * @method static Builder|CustomerCredit notSpent()
 * @method static Builder|CustomerCredit onlyTrashed()
 * @method static Builder|CustomerCredit query()
 * @method static Builder|CustomerCredit spent()
 * @method static Builder|CustomerCredit whereAmount($value)
 * @method static Builder|CustomerCredit whereCreatedAt($value)
 * @method static Builder|CustomerCredit whereCustomerId($value)
 * @method static Builder|CustomerCredit whereDeletedAt($value)
 * @method static Builder|CustomerCredit whereDescription($value)
 * @method static Builder|CustomerCredit whereId($value)
 * @method static Builder|CustomerCredit whereMeta($value)
 * @method static Builder|CustomerCredit whereSourceId($value)
 * @method static Builder|CustomerCredit whereSourceType($value)
 * @method static Builder|CustomerCredit whereSpentAt($value)
 * @method static Builder|CustomerCredit whereUpdatedAt($value)
 * @method static Builder|CustomerCredit withTrashed()
 * @method static Builder|CustomerCredit withoutTrashed()
 *
 * @mixin \Eloquent
 */
class CustomerCredit extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'spent_at' => 'datetime',
        'meta' => 'array',
    ];

    protected function usedAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): float => $this->payments()->type(PaymentType::Credit)->sum('amount'),
        );
    }

    protected function openAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): float => $attributes['amount'] - $this->used_amount,
        );
    }

    protected function isSpent(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => ($attributes['amount'] <= $this->used_amount) || ! is_null($attributes['spent_at']),
        );
    }

    public function scopeSpent(Builder $query): void
    {
        $query->whereNotNull('spent_at');
    }

    public function scopeNotSpent(Builder $query): void
    {
        $query->whereNull('spent_at');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo('source');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'reference');
    }
}
