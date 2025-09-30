<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPUnit\Framework\callback;

/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $name
 * @property bool $show_in_appointment
 * @property bool $is_required
 * @property bool $allow_multiple
 * @property bool $depends_on_branch
 * @property bool $depends_on_room
 * @property bool $depends_on_category
 * @property bool $depends_on_user
 * @property bool $depends_on_availability
 * @property array<array-key, mixed>|null $meta
 * @property-read Collection<int, ResourceField> $resourceFields
 * @property-read int|null $resource_fields_count
 * @property-read Collection<int, SystemResourceType> $systemResourceTypeDependants
 * @property-read int|null $system_resource_type_dependants_count
 * @property-read Collection<int, SystemResourceType> $systemResourceTypeDependencies
 * @property-read int|null $system_resource_type_dependencies_count
 * @property-read Collection<int, SystemResource> $systemResources
 * @property-read int|null $system_resources_count
 * @method static Builder<static>|SystemResourceType newModelQuery()
 * @method static Builder<static>|SystemResourceType newQuery()
 * @method static Builder<static>|SystemResourceType onlyTrashed()
 * @method static Builder<static>|SystemResourceType query()
 * @method static Builder<static>|SystemResourceType whereAllowMultiple($value)
 * @method static Builder<static>|SystemResourceType whereCreatedAt($value)
 * @method static Builder<static>|SystemResourceType whereDeletedAt($value)
 * @method static Builder<static>|SystemResourceType whereDependsOnBranch($value)
 * @method static Builder<static>|SystemResourceType whereDependsOnCategory($value)
 * @method static Builder<static>|SystemResourceType whereDependsOnRoom($value)
 * @method static Builder<static>|SystemResourceType whereDependsOnUser($value)
 * @method static Builder<static>|SystemResourceType whereId($value)
 * @method static Builder<static>|SystemResourceType whereIsRequired($value)
 * @method static Builder<static>|SystemResourceType whereMeta($value)
 * @method static Builder<static>|SystemResourceType whereName($value)
 * @method static Builder<static>|SystemResourceType whereShowInAppointment($value)
 * @method static Builder<static>|SystemResourceType whereUpdatedAt($value)
 * @method static Builder<static>|SystemResourceType withTrashed()
 * @method static Builder<static>|SystemResourceType withoutTrashed()
 * @mixin \Eloquent
 */
class SystemResourceType extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'show_in_appointment' => 'bool',
        'is_required' => 'bool',
        'allow_multiple' => 'bool',
        'depends_on_branch' => 'bool',
        'depends_on_room' => 'bool',
        'depends_on_category' => 'bool',
        'depends_on_user' => 'bool',
        'depends_on_availability' => 'bool',
        'meta' => 'array',
    ];

    protected $with = ['resourceFields'];

    public function getAvailableSystemResources(
        CarbonInterface $time,
        null|int|array $branch,
        null|int|array $category,
        null|int|array $user,
        null|int|array $room,
        array $dependencies,
    )
    {
        $time = $time->toImmutable();
        return $this->systemResources
            ->filter(function (SystemResource $resource) use ($time, $branch, $category, $user, $room, $dependencies) {
                if($this->depends_on_branch && !$resource->hasBranchDependency($branch)) {
                    return false;
                }

                if($this->depends_on_category && !$resource->hasCategoryDependency($category)) {
                    return false;
                }

                if($this->depends_on_user && !$resource->hasUserDependency($user)) {
                    return false;
                }

                if($this->depends_on_availability) {
                    if(!$resource->hasAvailability($time, $this->depends_on_room ? $room : null)) {
                        return false;
                    }
                } else if($this->depends_on_room && !$resource->hasRoomDependency($room)) {
                    return false;
                }

                foreach($this->systemResourceTypeDependencies as $dependency) {
                    $value = $dependencies[$dependency->name];

                    if(is_null($value)) {
                        return false;
                    }

                    if(is_array($value)) {
                        return $resource->systemResourceDependencies->whereIn('id', $value)->isNotEmpty();
                    }

                    if(is_string($value) || is_integer($value)) {
                        return $resource->systemResourceDependencies->where('id', $value)->isNotEmpty();
                    }
                }

                return true;
            });
    }

    public function systemResources() : HasMany
    {
        return $this->hasMany(SystemResource::class);
    }

    public function resourceFields(): HasMany
    {
        return $this->hasMany(ResourceField::class);
    }

    /**
     * System Resource Types that resources belonging to this type depend on
     *
     * @return BelongsToMany
     */
    public function systemResourceTypeDependencies(): BelongsToMany
    {
        return $this->belongsToMany(SystemResourceType::class, 'system_resource_type_dependency', 'system_resource_type_id', 'dependency_id');
    }

    /**
     * System Resource Types that have resources which depend on this type
     *
     * @return BelongsToMany
     */
    public function systemResourceTypeDependants(): BelongsToMany
    {
        return $this->belongsToMany(SystemResourceType::class, 'system_resource_type_dependency', 'dependency_id', 'system_resource_type_id');
    }
}
