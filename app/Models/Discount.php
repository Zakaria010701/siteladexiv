<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Transactions\DiscountType;
use App\Observers\DiscountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Discount
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $discountable_type
 * @property int $discountable_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property DiscountType $type
 * @property string $description
 * @property float|null $percentage
 * @property float $amount
 * @property mixed|null $meta
 * @property bool $permanent
 * @property-read Model|\Eloquent $discountable
 * @property-read Model|\Eloquent $source
 *
 * @method static Builder|Discount newModelQuery()
 * @method static Builder|Discount newQuery()
 * @method static Builder|Discount query()
 * @method static Builder|Discount whereAmount($value)
 * @method static Builder|Discount whereCreatedAt($value)
 * @method static Builder|Discount whereDescription($value)
 * @method static Builder|Discount whereDiscountableId($value)
 * @method static Builder|Discount whereDiscountableType($value)
 * @method static Builder|Discount whereId($value)
 * @method static Builder|Discount whereMeta($value)
 * @method static Builder|Discount wherePercentage($value)
 * @method static Builder|Discount whereSourceId($value)
 * @method static Builder|Discount whereSourceType($value)
 * @method static Builder|Discount whereType($value)
 * @method static Builder|Discount whereUpdatedAt($value)
 * @method static Builder|Discount wherePermanent($value)
 *
 * @property-read CustomerDiscount|null $customerDiscount
 *
 * @mixin \Eloquent
 */
#[ObservedBy([DiscountObserver::class])]
class Discount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => DiscountType::class,
        'amount' => 'float',
        'percentage' => 'float',
        'permanent' => 'boolean',
    ];

    public function discountable(): MorphTo
    {
        return $this->morphTo('discountable');
    }

    public function source(): MorphTo
    {
        return $this->morphTo('source');
    }

    public function customerDiscount(): HasOne
    {
        return $this->hasOne(CustomerDiscount::class, 'source_id');
    }
}
