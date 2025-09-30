<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $system_resource_type_id
 * @property string $name
 * @property array<array-key, mixed>|null $meta
 * @property-read Collection<int, Appointment> $appointments
 * @property-read int|null $appointments_count
 * @property-read Collection<int, Availability> $availabilities
 * @property-read int|null $availabilities_count
 * @property-read Collection<int, Branch> $branchDependencies
 * @property-read int|null $branch_dependencies_count
 * @property-read Collection<int, Category> $categoryDependencies
 * @property-read int|null $category_dependencies_count
 * @property-read Collection<int, ResourceValues> $resourceValues
 * @property-read int|null $resource_values_count
 * @property-read Collection<int, Room> $roomDependencies
 * @property-read int|null $room_dependencies_count
 * @property-read Collection<int, Service> $serviceDependencies
 * @property-read int|null $service_dependencies_count
 * @property-read Collection<int, SystemResource> $systemResourceDependants
 * @property-read int|null $system_resource_dependants_count
 * @property-read Collection<int, SystemResource> $systemResourceDependencies
 * @property-read int|null $system_resource_dependencies_count
 * @property-read SystemResourceType $systemResourceType
 * @property-read Collection<int, User> $userDependencies
 * @property-read int|null $user_dependencies_count
 * @method static Builder<static>|SystemResource hasValue(string $field, string $value, ?mixed $operator = null)
 * @method static Builder<static>|SystemResource newModelQuery()
 * @method static Builder<static>|SystemResource newQuery()
 * @method static Builder<static>|SystemResource onlyTrashed()
 * @method static Builder<static>|SystemResource query()
 * @method static Builder<static>|SystemResource whereCreatedAt($value)
 * @method static Builder<static>|SystemResource whereDeletedAt($value)
 * @method static Builder<static>|SystemResource whereId($value)
 * @method static Builder<static>|SystemResource whereMeta($value)
 * @method static Builder<static>|SystemResource whereName($value)
 * @method static Builder<static>|SystemResource whereSystemResourceTypeId($value)
 * @method static Builder<static>|SystemResource whereUpdatedAt($value)
 * @method static Builder<static>|SystemResource whereValue(string $field, string $value, ?mixed $operator = null)
 * @method static Builder<static>|SystemResource withTrashed()
 * @method static Builder<static>|SystemResource withoutTrashed()
 * @mixin \Eloquent
 */
class SystemResource extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    protected $with = ['systemResourceType', 'resourceValues',];

    public function getValue(int|string|ResourceField $field)
    {
        if(is_string($field)) {
            $field = $this->systemResourceType()->resourceFields()->where('name', $field)->first();
            if(is_null($field)) {
                return null;
            }
        }

        if($field instanceof ResourceField) {
            $field = $field->id;
        }

        return $this->resourceValues->where('resource_field_id', $field)->first()?->value ?? null;
    }

    public function availabilities(): MorphMany
    {
        return $this->morphMany(Availability::class, 'planable');
    }

    public function hasAvailability(CarbonInterface $time, null|int|array $room): bool
    {
        $this->loadMissing([
            'availabilities' =>  fn (Builder $query) => $query
                ->where('start_date', '<=', $time)
                ->where(fn (Builder $query) => $query->whereNull('end_date')->orWhere('end_date', '>=', $time))
        ]);

        /** @var ?Availability */
        $availability = $this->availabilities->first();

        if(is_null($availability) || $availability->isAvailableAt($time, $room)) {
            return false;
        }

        return true;
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class);
    }

    public function branchDependencies(): MorphToMany
    {
        return $this->morphedByMany(Branch::class, 'dependable', 'system_resource_dependables');
    }

    public function hasBranchDependency(null|int|array $branch): bool
    {
        return $this->branchDependencies->when(
            value: is_array($branch),
            callback: fn (Collection $collection) => $collection->whereIn('id', $branch),
            default: fn (Collection $collection) => $collection->where('id', $branch)
        )->isNotEmpty();
    }

    public function categoryDependencies(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'dependable', 'system_resource_dependables');
    }

    public function hasCategoryDependency(null|int|array $category): bool
    {
        return $this->categoryDependencies->when(
            value: is_array($category),
            callback: fn (Collection $collection) => $collection->whereIn('id', $category),
            default: fn (Collection $collection) => $collection->where('id', $category)
        )->isNotEmpty();
    }

    public function roomDependencies(): MorphToMany
    {
        return $this->morphedByMany(Room::class, 'dependable', 'system_resource_dependables');
    }

    public function hasRoomDependency(null|int|array $room): bool
    {
        return $this->roomDependencies->when(
            value: is_array($room),
            callback: fn (Collection $collection) => $collection->whereIn('id', $room),
            default: fn (Collection $collection) => $collection->where('id', $room)
        )->isNotEmpty();
    }

    public function serviceDependencies(): MorphToMany
    {
        return $this->morphedByMany(Service::class, 'dependable', 'system_resource_dependables');
    }

    public function hasServiceDependency(null|int|array $service): bool
    {
        return $this->serviceDependencies->when(
            value: is_array($service),
            callback: fn (Collection $collection) => $collection->whereIn('id', $service),
            default: fn (Collection $collection) => $collection->where('id', $service)
        )->isNotEmpty();
    }

    public function userDependencies(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'dependable', 'system_resource_dependables');
    }

    public function hasUserDependency(null|int|array $user): bool
    {
        return $this->userDependencies->when(
            value: is_array($user),
            callback: fn (Collection $collection) => $collection->whereIn('id', $user),
            default: fn (Collection $collection) => $collection->where('id', $user)
        )->isNotEmpty();
    }

    public function systemResourceDependencies(): MorphToMany
    {
        return $this->morphedByMany(SystemResource::class, 'dependable', 'system_resource_dependables');
    }

    public function systemResourceDependants(): MorphToMany
    {
        return $this->morphToMany(SystemResource::class, 'dependable', 'system_resource_dependables');
    }

    public function systemResourceType() : BelongsTo
    {
        return $this->belongsTo(SystemResourceType::class);
    }

    public function resourceValues(): HasMany
    {
        return $this->hasMany(ResourceValues::class);
    }

    public function scopeWhereValue(Builder $query, string $field, string $value, mixed $operator = null)
    {
        $query->whereHas('resourceValues', fn (Builder $query) => $query
            ->whereHas('resourceField', fn (Builder $query) => $query->where('name', $field))
            ->where('value', $operator, $value)
        );
    }

    public function scopeHasValue(Builder $query, string $field, string $value, mixed $operator = null)
    {
        $query->whereHas('resourceValues', fn (Builder $query) => $query
            ->whereHas('resourceField', fn (Builder $query) => $query->where('name', $field))
            ->whereNotNull('value')
            ->where('value', '!=', )
        );
    }

}
