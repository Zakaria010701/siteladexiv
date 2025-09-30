<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Enums\Appointments\AppointmentType;
use App\Enums\WorkTimes\WorkTimeType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\WorkTime
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $work_time_group_id
 * @property int $user_id
 * @property int $branch_id
 * @property int $room_id
 * @property WorkTimeType $type
 * @property Carbon $start
 * @property Carbon $end
 * @property-read Branch|null $branch
 * @property-read Room|null $room
 * @property-read User|null $user
 * @property-read WorkTimeGroup|null $workTimeGroup
 *
 * @method static Builder|WorkTime newModelQuery()
 * @method static Builder|WorkTime newQuery()
 * @method static Builder|WorkTime query()
 * @method static Builder|WorkTime whereBranchId($value)
 * @method static Builder|WorkTime whereCreatedAt($value)
 * @method static Builder|WorkTime whereEnd($value)
 * @method static Builder|WorkTime whereId($value)
 * @method static Builder|WorkTime whereRoomId($value)
 * @method static Builder|WorkTime whereStart($value)
 * @method static Builder|WorkTime whereType($value)
 * @method static Builder|WorkTime whereUpdatedAt($value)
 * @method static Builder|WorkTime whereUserId($value)
 * @method static Builder|WorkTime whereWorkTimeGroupId($value)
 *
 * @property-read mixed $title
 *
 * @mixin \Eloquent
 */
class WorkTime extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => WorkTimeType::class,
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => sprintf(
                '%s %s-%s',
                $this->user?->name,
                $this->start->format('Hi'),
                $this->end->format('Hi'),
            )
        );
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'room_id', 'room_id')
            ->where('start', '>=', $this->start->toIso8601String())
            ->where('start', '<=', $this->end->toIso8601String())
            ->where('type', '!=', AppointmentType::RoomBlock);
    }

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

    public function workTimeGroup(): BelongsTo
    {
        return $this->belongsTo(WorkTimeGroup::class);
    }
}
