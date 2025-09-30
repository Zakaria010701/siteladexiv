<?php

namespace App\Models;

use App\Observers\VoucherObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(VoucherObserver::class)]
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $customer_id
 * @property int|null $purchaser_id
 * @property int $voucher_nr
 * @property string $amount
 * @property string|null $description
 * @property array|null $meta
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\CustomerCredit|null $customerCredit
 * @property-read \App\Models\Customer|null $purchaser
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher wherePurchaserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher whereVoucherNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Voucher withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(VoucherObserver::class)]
class Voucher extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function isRedeemed(): Attribute
    {
        return new Attribute(
            get: fn () => isset($this->customerCredit)
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerCredit(): MorphOne
    {
        return $this->morphOne(CustomerCredit::class, 'source');
    }

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'purchaser_id');
    }

    public function scopeRedeemed(Builder $query): void
    {
        $query->whereHas('customerCredit');
    }

    public function scopeUnredeemed(Builder $query): void
    {
        $query->whereDoesntHave('customerCredit');
    }
}
