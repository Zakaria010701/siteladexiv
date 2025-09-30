<?php

namespace App\Models;

use App\Observers\TimeReportOverviewObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy([TimeReportOverviewObserver::class])]
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property int|null $previous_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $target_minutes
 * @property int $work_time_minutes
 * @property int $total_minutes
 * @property int $real_total_minutes
 * @property int $actual_minutes
 * @property int $overtime_minutes
 * @property int $uncapped_overtime_minutes
 * @property int $carry_overtime_minutes
 * @property int $manual_overtime_minutes
 * @property int $leave_days
 * @property int $sick_days
 * @property int $vacation_days
 * @property int $carry_vacation_days
 * @property int $manual_vacation_days
 * @property \Illuminate\Support\Carbon|null $edited_at
 * @property int|null $edited_by_id
 * @property \Illuminate\Support\Carbon|null $controlled_at
 * @property int|null $controlled_by_id
 * @property string|null $note
 * @property array|null $meta
 * @property-read \App\Models\User|null $controlledBy
 * @property-read \App\Models\User|null $editedBy
 * @property-read TimeReportOverview|null $next
 * @property-read TimeReportOverview|null $previous
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TimeReport> $timeReports
 * @property-read int|null $time_reports_count
 * @property-read mixed $total_overtime
 * @property-read mixed $total_vacation
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereActualMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereCarryOvertimeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereCarryVacationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereControlledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereControlledById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereEditedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereLeaveDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereManualOvertimeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereManualVacationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereOvertimeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview wherePreviousId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereRealTotalMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereSickDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereTargetMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereTotalMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereUncappedOvertimeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereVacationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeReportOverview whereWorkTimeMinutes($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy([TimeReportOverviewObserver::class])]
class TimeReportOverview extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'edited_at' => 'datetime',
        'controlled_at' => 'datetime',
        'meta' => 'array',
    ];

    public function totalOvertime(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['overtime_minutes'] + $attributes['carry_overtime_minutes'] + $attributes['manual_overtime_minutes'],
        );
    }

    public function totalVacation(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['carry_vacation_days'] + ($attributes['manual_vacation_days'] ?? 0) - ($attributes['vacation_days'] ?? 0),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timeReports(): HasMany
    {
        return $this->hasMany(TimeReport::class);
    }

    public function previous(): BelongsTo
    {
        return $this->belongsTo(TimeReportOverview::class, 'previous_id');
    }

    public function next(): HasOne
    {
        return $this->hasOne(TimeReportOverview::class, 'previous_id');
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
