<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Models\Contracts\AvailabilityEvent;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use phpseclib3\File\ASN1\Maps\Time;

/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $availability_id
 * @property int|null $room_id
 * @property string|null $start
 * @property int $target_minutes
 * @property Weekday|null $weekday
 * @property TimeStep $repeat_step
 * @property int $repeat_every
 * @property Carbon $start_date
 * @property-read Availability $availability
 * @property-read Room|null $room
 * @method static Builder<static>|AvailabilityShift newModelQuery()
 * @method static Builder<static>|AvailabilityShift newQuery()
 * @method static Builder<static>|AvailabilityShift query()
 * @method static Builder<static>|AvailabilityShift whereAvailabilityId($value)
 * @method static Builder<static>|AvailabilityShift whereCreatedAt($value)
 * @method static Builder<static>|AvailabilityShift whereId($value)
 * @method static Builder<static>|AvailabilityShift whereRepeatEvery($value)
 * @method static Builder<static>|AvailabilityShift whereRepeatStep($value)
 * @method static Builder<static>|AvailabilityShift whereRoomId($value)
 * @method static Builder<static>|AvailabilityShift whereStart($value)
 * @method static Builder<static>|AvailabilityShift whereStartDate($value)
 * @method static Builder<static>|AvailabilityShift whereTargetMinutes($value)
 * @method static Builder<static>|AvailabilityShift whereUpdatedAt($value)
 * @method static Builder<static>|AvailabilityShift whereWeekday($value)
 * @mixin \Eloquent
 */
class AvailabilityShift extends Model implements AvailabilityEvent
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'weekday' => Weekday::class,
        'repeat_step' => TimeStep::class,
    ];

    protected $with = ['room'];

    public function getStartTime(): ?string
    {
        return $this->start;
    }

    public function getEndTime(): ?string
    {
        return Carbon::parse($this->start)->addMinutes($this->target_minutes)->format('H:i');
    }

    public function getRoomId(): ?int
    {
        return $this->room_id;
    }

    public function getAvailabilityType(): ?AvailabilityType
    {
        return $this->availability->availabilityType;
    }

    public function getTargetMinutes(): ?int
    {
        return $this->target_minutes;
    }

    public function appliesOnDate(CarbonImmutable $date): bool
    {
        $start = $this->start_date;
        if($start->gt($date)) {
            return false;
        }

        if($this->repeat_step == TimeStep::Weeks && $date->dayOfWeek != $this->weekday?->value) {
            return false;
        }

        if($this->repeat_step == TimeStep::Months && $date->dayOfMonth != $this->day_of_month) {
            return false;
        }

        // Adjust the start value to the next specified weekday
        if($this->repeat_step == TimeStep::Weeks) {
            $start = $this->start_date->weekday($this->weekday->toCarbonWeekDay());
        }

        // Adjust the start value to the next specified day of the month
        if($this->repeat_step == TimeStep::Months) {
            $start = $this->start_date->startOfMonth()->addDays($this->day_of_month-1);
        }

        if($this->repeat_step->diff($start, $date) % $this->repeat_every !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Find all availability shifts between to dates and return them in a format parsebal by the calendar
     *
     * @param CarbonImmutable $start
     * @param CarbonImmutable $end
     * @return Collection
     */
    public function getShiftEventsBetween(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        // If no room for this shift has been set return an empty collection
        if(is_null($this->room_id)) {
            return collect();
        }

        $startCopy = $start->copy();

        // Adjust the start to be within the availability
        if($start->lt($this->availability->start_date)) {
            $start = $this->availability->start_date;
        }
        if(!is_null($this->availability->end_date) && $end->gt($this->availability->end_date)) {
            $start = $this->availability->end_date;
        }

        // Adjust the start value to the next specified weekday
        if($this->repeat_step == TimeStep::Weeks) {
            $start = $start->weekday($this->weekday->toCarbonWeekDay());

        }

        // Adjust the start value to the next specified day of the month
        if($this->repeat_step == TimeStep::Months) {
            $start = $start->startOfMonth()->addDays($this->day_of_month-1);
        }

        // Ensure that on repeat steps greater than 1 dates are properly skipped
        if($this->repeat_step->diff($this->availability->start_date, $start) % $this->repeat_every !== 0) {
            $start = $this->repeat_step->add($this->availability->start_date, $this->repeat_every);
        }

        // Check that the modified start value is within the original start & end value. Otherwise no events exist in that time frame
        if($start->gt($end) || $start->lt($startCopy)) {
            return collect();
        }

        $period = $this->repeat_step->getInterval($this->repeat_every)->toPeriod($start, $end);
        $shifts = collect();

        foreach ($period as $date) {
            $start = $date->setTimeFromTimeString($this->start);
            $end = $date->setTimeFromTimeString($this->end);

            // Add an all day or normal calendar event
            if($this->availability->is_all_day || !$this->availability->is_background) {
                $shifts->push([
                    'id' => $this->availability_id,
                    'title' => $this->availability->is_all_day
                    ? sprintf('%s %s-%s', $this->availability->title, $start->format('Hi'), $end->format('Hi'))
                    : sprintf("%s \n %s", $start->format('H:i'), $this->availability->title),
                    'start' => $start->format('Y-m-d H:i'),
                    'end' => $end->format('Y-m-d H:i'),
                    'resourceId' => $this->room_id,
                    'color' => $this->availability->color,
                    'allDay' => $this->availability->is_all_day,
                    'type' => 'availability',
                ]);
            }

            // Add a background event if the is_background flag is set
            if($this->availability->is_background) {
                $shifts->push([
                    'id' => $this->availability_id,
                    'start' => $start->format('Y-m-d H:i'),
                    'end' => $end->format('Y-m-d H:i'),
                    'resourceId' => $this->room_id,
                    //'color' => $this->availability->color,
                    'allDay' => false,
                    'display' => $this->availability->is_background_inverted ? 'inverse-background' : 'background',
                    'type' => 'availability',
                    'groupId' => 'availability',
                ]);
            }
        }

        return $shifts;
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
