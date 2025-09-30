<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\TimeStep;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\WorkTimeGroup
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property int $branch_id
 * @property int $room_id
 * @property string $type
 * @property string $start
 * @property string $end
 * @property Carbon $repeat_from
 * @property Carbon $repeat_till
 * @property int $repeat_every
 * @property TimeStep $repeat_step
 * @property-read Branch|null $branch
 * @property-read Room|null $room
 * @property-read User|null $user
 * @property-read Collection<int, WorkTime> $workTimes
 * @property-read int|null $work_times_count
 *
 * @method static Builder|WorkTimeGroup newModelQuery()
 * @method static Builder|WorkTimeGroup newQuery()
 * @method static Builder|WorkTimeGroup query()
 * @method static Builder|WorkTimeGroup whereBranchId($value)
 * @method static Builder|WorkTimeGroup whereCreatedAt($value)
 * @method static Builder|WorkTimeGroup whereEnd($value)
 * @method static Builder|WorkTimeGroup whereId($value)
 * @method static Builder|WorkTimeGroup whereRepeatFrom($value)
 * @method static Builder|WorkTimeGroup whereRepeatTill($value)
 * @method static Builder|WorkTimeGroup whereRepeatType($value)
 * @method static Builder|WorkTimeGroup whereRoomId($value)
 * @method static Builder|WorkTimeGroup whereStart($value)
 * @method static Builder|WorkTimeGroup whereType($value)
 * @method static Builder|WorkTimeGroup whereUpdatedAt($value)
 * @method static Builder|WorkTimeGroup whereUserId($value)
 * @method static Builder|WorkTimeGroup whereRepeatEvery($value)
 * @method static Builder|WorkTimeGroup whereRepeatStep($value)
 *
 * @mixin \Eloquent
 */
class WorkTimeGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'repeat_from' => 'date',
        'repeat_till' => 'date',
        'repeat_step' => TimeStep::class,
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workTimes(): HasMany
    {
        return $this->hasMany(WorkTime::class);
    }
}
