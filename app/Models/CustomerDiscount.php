<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\CustomerDiscount
 *
 * @property-read Customer|null $customer
 * @property-read Collection<int, Service> $services
 * @property-read int|null $service_count
 * @property-read Discount|null $source
 *
 * @method static Builder|CustomerDiscount newModelQuery()
 * @method static Builder|CustomerDiscount newQuery()
 * @method static Builder|CustomerDiscount query()
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $customer_id
 * @property int|null $source_id
 * @property string $description
 * @property float|null $percentage
 * @property float|null $amount
 *
 * @method static Builder|CustomerDiscount whereAmount($value)
 * @method static Builder|CustomerDiscount whereCreatedAt($value)
 * @method static Builder|CustomerDiscount whereCustomerId($value)
 * @method static Builder|CustomerDiscount whereDescription($value)
 * @method static Builder|CustomerDiscount whereId($value)
 * @method static Builder|CustomerDiscount wherePercentage($value)
 * @method static Builder|CustomerDiscount whereSourceId($value)
 * @method static Builder|CustomerDiscount whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CustomerDiscount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'float',
        'percentage' => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'source_id');
    }
}
