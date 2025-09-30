<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\TimeRecords\LeaveType;
use App\Enums\TimeRecords\TimeCheckStatus;
use App\Observers\TimeReportObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property Carbon $date
 * @property int $target_minutes
 * @property Carbon|null $work_time_start
 * @property Carbon|null $work_time_end
 * @property int $work_time_minutes
 * @property Carbon|null $time_in
 * @property TimeCheckStatus|null $time_in_status
 * @property Carbon|null $time_out
 * @property TimeCheckStatus|null $time_out_status
 * @property int $total_minutes
 * @property Carbon|null $real_time_in
 * @property Carbon|null $real_time_out
 * @property int $real_total_minutes
 * @property int $break_minutes
 * @property int $actual_minutes
 * @property int $manual_minutes
 * @property int $overtime_minutes
 * @property int $uncapped_overtime_minutes
 * @property bool $is_overtime_capped
 * @property LeaveType|null $leave_type
 * @property string|null $edited_at
 * @property int|null $edited_by_id
 * @property Carbon|null $controlled_at
 * @property int|null $controlled_by_id
 * @property string|null $note
 * @property array|null $meta
 * @property-read User|null $controlledBy
 * @property-read User|null $editedBy
 * @property-read User $user
 *
 * @method static Builder|TimeReport newModelQuery()
 * @method static Builder|TimeReport newQuery()
 * @method static Builder|TimeReport query()
 * @method static Builder|TimeReport whereActualMinutes($value)
 * @method static Builder|TimeReport whereBreakMinutes($value)
 * @method static Builder|TimeReport whereControlledAt($value)
 * @method static Builder|TimeReport whereControlledById($value)
 * @method static Builder|TimeReport whereCreatedAt($value)
 * @method static Builder|TimeReport whereDate($value)
 * @method static Builder|TimeReport whereEditedAt($value)
 * @method static Builder|TimeReport whereEditedById($value)
 * @method static Builder|TimeReport whereId($value)
 * @method static Builder|TimeReport whereIsOvertimeCapped($value)
 * @method static Builder|TimeReport whereLeaveType($value)
 * @method static Builder|TimeReport whereManualMinutes($value)
 * @method static Builder|TimeReport whereMeta($value)
 * @method static Builder|TimeReport whereNote($value)
 * @method static Builder|TimeReport whereOvertimeMinutes($value)
 * @method static Builder|TimeReport whereRealTimeIn($value)
 * @method static Builder|TimeReport whereRealTimeOut($value)
 * @method static Builder|TimeReport whereRealTotalMinutes($value)
 * @method static Builder|TimeReport whereTargetMinutes($value)
 * @method static Builder|TimeReport whereTimeIn($value)
 * @method static Builder|TimeReport whereTimeInStatus($value)
 * @method static Builder|TimeReport whereTimeOut($value)
 * @method static Builder|TimeReport whereTimeOutStatus($value)
 * @method static Builder|TimeReport whereTotalMinutes($value)
 * @method static Builder|TimeReport whereUncappedOvertimeMinutes($value)
 * @method static Builder|TimeReport whereUpdatedAt($value)
 * @method static Builder|TimeReport whereUserId($value)
 * @method static Builder|TimeReport whereWorkTimeEnd($value)
 * @method static Builder|TimeReport whereWorkTimeMinutes($value)
 * @method static Builder|TimeReport whereWorkTimeStart($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy([TimeReportObserver::class])]
class TimeReport extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'work_time_start' => 'datetime',
        'work_time_end' => 'datetime',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'real_time_in' => 'datetime',
        'real_time_out' => 'datetime',
        'controlled_at' => 'datetime',
        'leave_type' => LeaveType::class,
        'time_in_status' => TimeCheckStatus::class,
        'time_out_status' => TimeCheckStatus::class,
        'meta' => 'array',
        'is_overtime_capped' => 'boolean',
    ];

    public function isEdited(): bool
    {
        return ! is_null($this->edited_at);
    }

    public function needsToBeControlled(): bool
    {
        return ! is_null($this->edited_at) && is_null($this->controlled_at);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timeReportOverview(): BelongsTo
    {
        return $this->belongsTo(TimeReportOverview::class);
    }

    public function payroll(): HasOne
    {
        return $this->hasOne(Payroll::class);
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by_id');
    }

    public function controlledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controlled_by_id');
    }
}
