<?php

namespace App\Support\Calendar;

use App\Actions\Appointments\CalculateDuration;
use App\DataObjects\Calendar\CalendarOpening;
use App\Enums\Appointments\AppointmentType;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\AvailabilityType;
use App\Models\Branch;
use App\Models\Contracts\AvailabilityEvent;
use App\Models\Room;
use App\Models\Service;
use App\Models\SystemResource;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class CalendarOpeningCalculator
{

    private Collection $availabilityTypes;

    public function __construct(
        private CarbonImmutable $start,
        private CarbonImmutable $end,
        private AppointmentType $appointmentType,
        private Collection $services,
        private Collection $branches,
        private Collection $rooms,
        private Collection $users,
        private Collection $resources,
        private int $duration,
    ) {
        $this->availabilityTypes = AvailabilityType::query()
            ->where('is_hidden', false)
            ->where('is_background_inverted', true)
            ->where('is_all_day', true)
            ->get();
    }

    public static function make(
        string|CarbonInterface $start,
        string|CarbonInterface $end,
        string|AppointmentType $appointmentType,
        array|Collection $services,
        null|array|Collection $branches = null,
        null|array|Collection $rooms = null,
        null|array|Collection $users = null,
        null|array|Collection $resources = null,
        null|int $duration = null,
    ): self
    {

        if(is_string($start)) {
            $start = CarbonImmutable::parse($start);
        }

        if(is_string($end)) {
            $end = CarbonImmutable::parse($end);
        }

        if(is_string($appointmentType)) {
            $appointmentType = AppointmentType::from($appointmentType);
        }

        if(is_array($services)) {
            $services = Service::query()->whereIn('id', $services)->get();
        }

        $serviceCount = $services->count();

        if(is_null($branches)) {
            $branches = Branch::query()
                ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        } else if(is_array($branches)) {
            $branches = Branch::query()
                ->whereIn('id', $branches)
                ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        }

        if(is_null($rooms)) {
            $rooms = Room::query()
                ->whereIn('branch_id', $branches->pluck('id')->toArray())
                ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        } else if(is_array($rooms)) {
            $rooms = Room::query()
                ->whereIn('id', $rooms)
                ->whereIn('branch_id', $branches->pluck('id')->toArray())
                ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        }

        if(is_null($users)) {
            $users = User::query()
                ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        } else if(is_array($users)) {
            $users = User::query()
                ->whereIn('id', $users)
                ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        }

        if(is_null($resources)) {
            $resources = SystemResource::query()
                ->whereHas('serviceDependencies', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        } else if(is_array($resources)) {
            $resources = SystemResource::query()
                ->whereIn('id', $resources)
                ->whereHas('serviceDependencies', fn (Builder $query) => $query->whereIn('services.id', $services->pluck('id')->toArray()), '>=', $serviceCount)
                ->get();
        }

        if(is_null($duration)) {
            $duration = CalculateDuration::make($appointmentType, $services)->execute();
        }

        return new self(
            $start->toImmutable(),
            $end->toImmutable(),
            $appointmentType,
            $services,
            $branches,
            $rooms,
            $users,
            $resources,
            $duration,
        );
    }

    public function findCalendarSlots(): Collection
    {
        $slots = collect();

        $this->findCalendarOpenings()->each(function (CalendarOpening $opening) use (&$slots) {
            $interval = CarbonInterval::minutes(frontend()->slot_step)->toPeriod($opening->start, $opening->end);
            foreach ($interval as $slot) {
                $end = $slot->copy()->addMinutes($this->duration);
                if ($end->gt($opening->end)) {
                    continue;
                }

                $slots->push(new CalendarOpening(
                    start: $slot->toImmutable(),
                    end: $end->toImmutable(),
                    room: $opening->room,
                    availability: $opening->availability,
                    user: $opening->user,
                    resources: $opening->resources,
                ));
            }
        });

        return $slots->sortBy(fn (CalendarOpening $opening) => $opening->start);
    }

    public function findCalendarOpenings(): Collection
    {
        $userOpenings = $this->getUserOpenings();
        $resourceOpenings = $this->getResourceOpenings();

        $overlaps = collect();
        foreach($userOpenings as $user) {
            foreach($resourceOpenings as $resource) {
                $latestStart = $user['start']->max($resource['start']);
                $earliestEnd = $user['end']->min($resource['end']);
                if(
                    $latestStart->lt($earliestEnd)
                    && $latestStart->diffInMinutes($earliestEnd) >= $this->duration
                    && $user['room']->id == $resource['room']->id
                ) {

                    $overlaps->push(new CalendarOpening(
                        start: $latestStart->toImmutable(),
                        end: $earliestEnd->toImmutable(),
                        room: $user['room'],
                        availability: $user['availabilityEvent'],
                        user: $user['user'],
                        resources: $resource['resources'],
                    ));
                }

            }
        }

        $overlaps = $overlaps->unique(fn (CalendarOpening $opening) => sprintf("%s-%s-%s-%s",
            $opening->start->toISOString(),
            $opening->end->toISOString(),
            $opening->user->id,
            $opening->room->id,
        ))->sortBy('start');

        return $overlaps;
    }

    private function getUserOpenings(): Collection
    {
        $users = $this->getUserAvailabilities();
        $appointments = $this->getAppointments();

        $openings = collect();
        foreach ($users as $user) {
            /** @var CarbonInterface $startOpen */
            $startOpen = $user['start'];
            /** @var CarbonInterface $endOpen */
            $endOpen = $user['end'];
            /** @var Appointment $appointment */
            foreach ($appointments as $appointment) {
                if($appointment->room_id != $user['room']->id) {
                    continue;
                }

                if($appointment->start->gte($endOpen)) {
                    continue;
                }

                if($startOpen->diffInMinutes($appointment->start) < $this->duration) {
                    if($startOpen->lt($appointment->end)) {
                        $startOpen = $appointment->end;
                    }
                    continue;
                }

                $openings->push([
                    'start' => $startOpen,
                    'end' => $appointment->start,
                    'room' => $user['room'],
                    'availabilityEvent' => $user['record'],
                    'user' => $user['planable'],
                ]);

                $startOpen = $appointment->end;
            }
            if($startOpen->lt($endOpen) && $startOpen->diffInMinutes($endOpen) >= $this->duration) {
                $openings->push([
                    'start' => $startOpen,
                    'end' => $endOpen,
                    'room' => $user['room'],
                    'availabilityEvent' => $user['record'],
                    'user' => $user['planable'],
                ]);
            }
        }
        return $openings;
    }

    private function getAppointments(): Collection
    {
        return Appointment::query()
            ->where('start', '<=', $this->end)
            ->where('end', '>=', $this->start)
            ->whereIn('room_id', $this->rooms->pluck('id')->toArray())
            ->get();
    }

    private function getUserAvailabilities(): Collection
    {
        return Availability::query()
            ->with([
                'availabilityShifts' => fn (EloquentBuilder $query) => $query
                    ->whereIn('room_id', $this->rooms->pluck('id')->toArray())
                    ->where('start_date', '<=', $this->end)
                    ->whereNotNull('start'),
                'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                    ->where('start_date', '<=', $this->end)
                    ->where('end_date', '>=', $this->start),
                'availabilityExceptions' => fn (EloquentBuilder $query) => $query
                    ->where('date', '>=', $this->start)
                    ->where('date', '<=', $this->end)
                    ->whereIn('room_id', $this->rooms->pluck('id')->toArray())
                    ->whereIn('availability_type_id', $this->availabilityTypes->pluck('id')->toArray())
                    ->whereNotNull('start'),
            ])
            ->where('planable_type', User::class)
            ->whereIn('planable_id', $this->users->pluck('id')->toArray())
            ->where('is_background_inverted', true)
            ->where('is_hidden', false)
            ->where('start_date', '<=', $this->end)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $this->start)
                ->orWhereNull('end_date'))
            ->get()
            ->map(fn (Availability $record) => $record->getRecordsBetween($this->start, $this->end))
            ->flatten(1);
    }

    private function getResourceAvailabilities(): Collection
    {
        $availabilityDependand = $this->resources->filter(fn (SystemResource $resource) => $resource->systemResourceType->depends_on_availability);
        $availabilityUndependand = $this->resources->diff($availabilityDependand);
        /** @var Collection */
        $availabilities = Availability::query()
            ->with([
                'availabilityShifts' => fn (EloquentBuilder $query) => $query
                    ->whereIn('room_id', $this->rooms->pluck('id')->toArray())
                    ->where('start_date', '<=', $this->end)
                    ->whereNotNull('start'),
                'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                    ->where('start_date', '<=', $this->end)
                    ->where('end_date', '>=', $this->start),
                'availabilityExceptions' => fn (EloquentBuilder $query) => $query
                    ->where('date', '>=', $this->start)
                    ->where('date', '<=', $this->end)
                    ->whereIn('room_id', $this->rooms->pluck('id')->toArray())
                    ->whereNotNull('start'),
                'planable',
            ])
            ->where('planable_type', SystemResource::class)
            ->whereIn('planable_id', $availabilityDependand->pluck('id')->toArray())
            ->where('start_date', '<=', $this->end)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $this->start)
                ->orWhereNull('end_date'))
            ->get()
            ->mapToGroups(fn (Availability $record) => [$record->planable->system_resource_type_id => $record->getRecordsBetween($this->start, $this->end)])
            ->map(fn (Collection $group) => $group->flatten(1));

        $availabilityUndependand->loadMissing('roomDependencies');
        $availabilityUndependand->each(function (SystemResource $resource) use (&$availabilities) {
            $resourceAvailabilities = $resource->roomDependencies->map(fn (Room $room) => [
                'start' => $this->start,
                'end' => $this->end,
                'room' => $room,
                'planable' => $resource,
            ]);
            $group = $availabilities->get($resource->system_resource_type_id);
            if(is_null($group)) {
                $group = $resourceAvailabilities;
            } else {
                $group->merge($resourceAvailabilities);
            }
            $availabilities->put($resource->system_resource_type_id, collect($group));
        });

        return $availabilities;
    }

    private function getResourceOpenings(): Collection
    {
        $resources = $this->getResourceAvailabilities();
        $openings = collect();

        foreach($resources as $group) {

            $openings = $this->findOpeningsForResources($group, $openings);
        }

        return $openings;
    }

    private function findOpeningsForResources(Collection $group, Collection $openings): Collection
    {
        if($openings->isEmpty()) {
            return $group->map(fn (array $resource) => [
                'start' => $resource['start'],
                'end' => $resource['end'],
                'room' => $resource['room'],
                'resources' => [$resource['planable']],
            ]);
        }
        $overlaps = collect();
        foreach($group as $resource) {
            foreach($openings as $opening) {
                $latestStart = $resource['start']->max($opening['start']);
                $earliestEnd = $resource['end']->min($opening['end']);
                if(
                    $latestStart->lt($earliestEnd)
                    && $latestStart->diffInMinutes($earliestEnd) >= $this->duration
                    && $resource['room']->id == $opening['room']->id
                ) {
                    $resources = $opening['resources'];
                    $resources[] = $resource['planable'];

                    $overlaps->push([
                        'start' => $latestStart,
                        'end' => $earliestEnd,
                        'room' => $resource['room'],
                        'resources' => $resources,
                    ]);
                }

            }
        }
        return $overlaps;
    }
}
