<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\TimeRecords\TimeConstraint;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property TimeConstraint $time_constraint
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|UserWorkType newModelQuery()
 * @method static Builder|UserWorkType newQuery()
 * @method static Builder|UserWorkType query()
 * @method static Builder|UserWorkType whereCreatedAt($value)
 * @method static Builder|UserWorkType whereId($value)
 * @method static Builder|UserWorkType whereName($value)
 * @method static Builder|UserWorkType whereTimeConstraint($value)
 * @method static Builder|UserWorkType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UserWorkType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'time_constraint' => TimeConstraint::class,
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
