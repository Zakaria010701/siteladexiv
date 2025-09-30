<?php

namespace App\Models;

use App\Enums\Gender;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $category_id
 * @property int|null $customer_id
 * @property string $name
 * @property string $short_code
 * @property Gender $gender
 * @property string|null $discount_percentage
 * @property string|null $discount
 * @property string|null $price
 * @property-read Category $category
 * @property-read Customer|null $customer
 * @property-read Collection<int, Service> $services
 * @property-read int|null $services_count
 *
 * @method static Builder|ServicePackage newModelQuery()
 * @method static Builder|ServicePackage newQuery()
 * @method static Builder|ServicePackage onlyTrashed()
 * @method static Builder|ServicePackage query()
 * @method static Builder|ServicePackage whereCategoryId($value)
 * @method static Builder|ServicePackage whereCreatedAt($value)
 * @method static Builder|ServicePackage whereCustomerId($value)
 * @method static Builder|ServicePackage whereDeletedAt($value)
 * @method static Builder|ServicePackage whereDiscount($value)
 * @method static Builder|ServicePackage whereDiscountPercentage($value)
 * @method static Builder|ServicePackage whereGender($value)
 * @method static Builder|ServicePackage whereId($value)
 * @method static Builder|ServicePackage whereName($value)
 * @method static Builder|ServicePackage wherePrice($value)
 * @method static Builder|ServicePackage whereShortCode($value)
 * @method static Builder|ServicePackage whereUpdatedAt($value)
 * @method static Builder|ServicePackage withTrashed()
 * @method static Builder|ServicePackage withoutTrashed()
 *
 * @mixin Eloquent
 */
class ServicePackage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'gender' => Gender::class,
        'meta' => 'array',
    ];

    public function title(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value, array $attributes) => sprintf(
                "%s (%s) [%s]",
                $attributes['name'],
                $attributes['short_code'],
                $this->services->implode('short_code', ', ')
            )
        );
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class)->withTimestamps();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}
