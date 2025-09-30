<?php

namespace App\Models;

use App\Enums\TimeRecords\TimeConstraint;
use App\Enums\User\WageType;
use App\Observers\TimePlanObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([TimePlanObserver::class])]
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property int $monday_hours
 * @property int $tuesday_hours
 * @property int $wednesday_hours
 * @property int $thursday_hours
 * @property int $friday_hours
 * @property int $saturday_hours
 * @property int $sunday_hours
 * @property TimeConstraint $time_constraint
 * @property int $yearly_vacation_days
 * @property int $start_vacation_days
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereFridayHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereMondayHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereSaturdayHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereSundayHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereThursdayHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereTimeConstraint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereTuesdayHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereWednesdayHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereStartVacationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimePlan whereYearlyVacationDays($value)
 *
 * @mixin \Eloquent
 */
class TimePlan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'time_constraint' => TimeConstraint::class,
        'wage_type' => WageType::class,
        'meta' => 'array',
    ];

    public function getTargetMinutes(CarbonInterface $date): int
    {
        return match ($date->dayOfWeek) {
            CarbonInterface::MONDAY => $this->monday_hours,
            CarbonInterface::TUESDAY => $this->tuesday_hours,
            CarbonInterface::WEDNESDAY => $this->wednesday_hours,
            CarbonInterface::THURSDAY => $this->thursday_hours,
            CarbonInterface::FRIDAY => $this->friday_hours,
            CarbonInterface::SATURDAY => $this->saturday_hours,
            CarbonInterface::SUNDAY => $this->sunday_hours,
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
