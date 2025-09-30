<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Transactions\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\DiscountTemplate
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property DiscountType $type
 * @property int|null $quantity
 * @property float|null $percentage
 * @property float|null $amount
 * @property-read Collection<int, Discount> $discounts
 * @property-read int|null $discounts_count
 *
 * @method static Builder|DiscountTemplate newModelQuery()
 * @method static Builder|DiscountTemplate newQuery()
 * @method static Builder|DiscountTemplate onlyTrashed()
 * @method static Builder|DiscountTemplate query()
 * @method static Builder|DiscountTemplate whereAmount($value)
 * @method static Builder|DiscountTemplate whereCreatedAt($value)
 * @method static Builder|DiscountTemplate whereDeletedAt($value)
 * @method static Builder|DiscountTemplate whereId($value)
 * @method static Builder|DiscountTemplate wherePercentage($value)
 * @method static Builder|DiscountTemplate whereQuantity($value)
 * @method static Builder|DiscountTemplate whereType($value)
 * @method static Builder|DiscountTemplate whereUpdatedAt($value)
 * @method static Builder|DiscountTemplate withTrashed()
 * @method static Builder|DiscountTemplate withoutTrashed()
 *
 * @mixin \Eloquent
 */
class DiscountTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => DiscountType::class,
        'amount' => 'float',
        'percentage' => 'float',
    ];

    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'source');
    }
}
