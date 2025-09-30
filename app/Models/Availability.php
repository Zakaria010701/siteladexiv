<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Actions\TimeReport\UpdateTimeReportRange;
use App\Filament\Admin\Clusters\Settings\Resources\Branches\BranchResource;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\Contracts\AvailabilityEvent;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $planable_type
 * @property int $planable_id
 * @property string $title
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $color
 * @property bool $is_hidden
 * @property bool $is_all_day
 * @property bool $is_background
 * @property bool $is_background_inverted
 * @property array<array-key, mixed>|null $meta
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AvailabilityAbsence> $availabilityAbsences
 * @property-read int|null $availability_absences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AvailabilityException> $availabilityExceptions
 * @property-read int|null $availability_exceptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AvailabilityShift> $availabilityShifts
 * @property-read int|null $availability_shifts_count
 * @property-read AvailabilityUserOption|null $availabilityUserOption
 * @property-read Model|\Eloquent $planable
 * @method static Builder<static>|Availability newModelQuery()
 * @method static Builder<static>|Availability newQuery()
 * @method static Builder<static>|Availability onlyTrashed()
 * @method static Builder<static>|Availability query()
 * @method static Builder<static>|Availability whereColor($value)
 * @method static Builder<static>|Availability whereCreatedAt($value)
 * @method static Builder<static>|Availability whereDeletedAt($value)
 * @method static Builder<static>|Availability whereEndDate($value)
 * @method static Builder<static>|Availability whereId($value)
 * @method static Builder<static>|Availability whereIsAllDay($value)
 * @method static Builder<static>|Availability whereIsBackground($value)
 * @method static Builder<static>|Availability whereIsBackgroundInverted($value)
 * @method static Builder<static>|Availability whereIsHidden($value)
 * @method static Builder<static>|Availability whereMeta($value)
 * @method static Builder<static>|Availability wherePlanableId($value)
 * @method static Builder<static>|Availability wherePlanableType($value)
 * @method static Builder<static>|Availability whereStartDate($value)
 * @method static Builder<static>|Availability whereTitle($value)
 * @method static Builder<static>|Availability whereUpdatedAt($value)
 * @method static Builder<static>|Availability withTrashed()
 * @method static Builder<static>|Availability withoutTrashed()
 * @mixin \Eloquent
 */
class Availability extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_hidden' => 'boolean',
        'is_all_day' => 'boolean',
        'is_background' => 'boolean',
        'is_background_inverted' => 'boolean',
        'meta' => 'array',
    ];

    protected $with = ['availabilityShifts', 'availabilityType'];

    /**
     * Updates the time report if the availability belongs to a user.
     *
     * @return void
     */
    public function updateTimeReport(): void
    {
        if(!$this->planable instanceof User) {
            return;
        }

        UpdateTimeReportRange::make($this->start_date, $this->end_date ?? today(), $this->planable)->excecute();
    }

    public function isAvailableAt(CarbonImmutable $time, null|int|array|Room $room = null): bool
    {
        $records = $this->getRecordsForDate($time);

        if($room instanceof Room) {
            $room = $room->id;
        }

        return $records->filter(function (AvailabilityEvent $record) use ($time, $room) {
            return $time->setTimeFromTimeString($record->getStartTime() ?? '00:00')->lte($time)
                && $time->setTimeFromTimeString($record->getEndTime())->gte($time)
                && (is_null($room) || (is_array($room) ? in_array($record->getRoomId(), $room) : $record->getRoomId() == $room));
        })->isNotEmpty();
    }

    public function getRecordsForDate(CarbonImmutable $date): Collection
    {

        // Find Exceptions for the date
        $exceptions = $this->availabilityExceptions
            ->filter(fn (AvailabilityException $record) => $record->date->startOfDay()->eq($date->startOfDay()));

        if($exceptions->isNotEmpty()) {
            // If Exceptions exist, only return the exceptions
            return $exceptions;
        }

        // Find Absences for the date
        $absences = $this->availabilityAbsences
            ->filter(fn (AvailabilityAbsence $record) => $record->start_date->lte($date) && $record->end_date->gte($date));

        if($absences->isNotEmpty()) {
            // If Absences exist return nothing
            return collect();
        }

        $shifts = $this->availabilityShifts
            ->filter(fn (AvailabilityShift $record) => $record->appliesOnDate($date->startOfDay()));
        return $shifts;
    }

    public function getRecordsBetween(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        $period = $start->toPeriod($end, 1, 'days');
        $records = collect();
        foreach($period as $date) {
            $tmp = $this->getRecordsForDate($date->toImmutable())
                ->map(fn (AvailabilityEvent $record) => [
                    'record' => $record,
                    'planable' => $this->planable,
                    'date' => $date,
                    'start' => $date->setTimeFromTimeString($record->getStartTime() ?? '09:00'),
                    'end' => $date->setTimeFromTimeString($record->getEndTime() ??  $date->setTimeFromTimeString($record->getStartTime() ?? '09:00')->addMinutes($record->getTargetMinutes())),
                    'room' => $record->room,
                ]);
            if($tmp->isNotEmpty()) {
                $records->push($tmp);
            }
        }

        return $records->flatten(1);
    }

    public function getEventsBetween(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        $period = $start->toPeriod($end, 1, 'days');
        $events = collect();
        foreach($period as $date) {
            $tmp = $this->getEventsForDate($date->toImmutable());
            if($tmp->isNotEmpty()) {
                $events->push($tmp);
            }
        }

        return $events->flatten(1);
    }

    public function getEventsForDate(CarbonImmutable $date): Collection
    {
        return $this->getRecordsForDate($date)
            ->reject(fn (AvailabilityEvent $record) => empty($record->room_id) || empty($record->getStartTime()) || empty($record->getEndTime()))
            ->map(fn (AvailabilityEvent $record) => $this->getEventsForTime($date, $record))
            ->flatten(1);
    }

    public function getEventsForTime(CarbonImmutable $date, AvailabilityEvent $event) : Collection
    {
        $start = $date->setTimeFromTimeString($event->getStartTime());
        $end = $date->setTimeFromTimeString($event->getEndTime());
        $type = $event->getAvailabilityType();
        $events = collect();
        $isAllDay = $type->is_all_day ?? $this->is_all_day;
        $isBackground = $type->is_background ?? $this->is_background;
        $isBackgroundInv = $type->is_background_inverted ?? $this->is_background_inverted;
        // Add an all day or normal calendar event
        if($isAllDay || !$isBackground) {
            $events->push([
                'id' => $this->id,
                'title' => $isAllDay
                ? sprintf('%s %s-%s', $this->title, $start->format('Hi'), $end->format('Hi'))
                : sprintf("%s \n %s", $start->format('H:i'), $this->title),
                'start' => $start,
                'end' => $end,
                'resourceId' => $event->getRoomId(),
                'color' => $type->color ?? $this->color,
                'allDay' => $isAllDay,
                'type' => 'availability',
            ]);
        }

        // Add a background event if the is_background flag is set
        if($isBackground) {
            $events->push([
                'id' => $this->id,
                'start' => $start,
                'end' => $end,
                'resourceId' => $event->getRoomId(),
                //'color' => $this->availability->color,
                'allDay' => false,
                'display' => $isBackgroundInv ? 'inverse-background' : 'background',
                'type' => 'availability',
                'groupId' => $type->group ?? $this->group,
            ]);
        }
        return $events;
    }

    public function planable(): MorphTo
    {
        return $this->morphTo('planable');
    }

    public function getPlanableUrl()
    {
        if ($this->planable == null) {
            return null;
        }

        if ($this->planable instanceof User) {
            return UserResource::getUrl('edit', ['record' => $this->planable]);
        }

        if ($this->planable instanceof Branch) {
            return BranchResource::getUrl('edit', ['record' => $this->planable]);
        }

        if ($this->planable instanceof SystemResource) {
            return SystemResourceResource::getUrl('edit', ['record' => $this->planable]);
        }

        return null;
    }

    public function getPlanableTitle()
    {
        if ($this->planable == null) {
            return null;
        }

        if ($this->planable instanceof User) {
            return $this->planable->full_name;
        }

        if ($this->planable instanceof Branch) {
            return $this->planable->name;
        }

        if ($this->planable instanceof SystemResource) {
            return $this->planable->name;
        }

        return null;
    }

    public function availabilityAbsences(): HasMany
    {
        return $this->hasMany(AvailabilityAbsence::class)->chaperone();
    }

    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(AvailabilityException::class)->chaperone();
    }

    public function availabilityType(): BelongsTo
    {
        return $this->belongsTo(AvailabilityType::class);
    }

    public function availabilityUserOption(): HasOne
    {
        return $this->hasOne(AvailabilityUserOption::class)->chaperone();
    }

    public function availabilityShifts(): HasMany
    {
        return $this->hasMany(AvailabilityShift::class)->chaperone();
    }

}
