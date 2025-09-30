<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\TimeStep;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Service
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $category_id
 * @property string $name
 * @property string $short_code
 * @property int $duration
 * @property string $price
 * @property-read Category|null $category
 * @property-read Collection<int, ServicePackage> $servicePackages
 * @property-read int|null $service_packages_count
 *
 * @method static Builder|Service newModelQuery()
 * @method static Builder|Service newQuery()
 * @method static Builder|Service onlyTrashed()
 * @method static Builder|Service query()
 * @method static Builder|Service whereCategoryId($value)
 * @method static Builder|Service whereCreatedAt($value)
 * @method static Builder|Service whereDeletedAt($value)
 * @method static Builder|Service whereDuration($value)
 * @method static Builder|Service whereId($value)
 * @method static Builder|Service whereName($value)
 * @method static Builder|Service wherePrice($value)
 * @method static Builder|Service whereShortCode($value)
 * @method static Builder|Service whereUpdatedAt($value)
 * @method static Builder|Service withTrashed()
 * @method static Builder|Service withoutTrashed()
 *
 * @property-read Collection<int, ServiceCredit> $serviceCredits
 * @property-read int|null $service_credits_count
 *
 * @mixin \Eloquent
 */
class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'next_appointment_step' => TimeStep::class,
    ];

    public function title(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value, array $attributes) => "{$attributes['name']} ({$attributes['short_code']})"
        );
    }

    public function appointmentItems(): MorphMany
    {
        return $this->morphMany(AppointmentItem::class, 'purchasable');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }

    public function serviceCredits(): HasMany
    {
        return $this->hasMany(ServiceCredit::class);
    }

    public function servicePackages(): BelongsToMany
    {
        return $this->belongsToMany(ServicePackage::class);
    }

    public function systemResources(): MorphToMany
    {
        return $this->morphToMany(SystemResource::class, 'dependable', 'system_resource_dependables');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
