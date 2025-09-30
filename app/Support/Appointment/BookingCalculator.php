<?php

namespace App\Support\Appointment;

use App\Actions\Appointments\CalculateDuration;
use App\Actions\Users\FindAvailableProviders;
use App\Enums\Appointments\AppointmentType;
use App\Enums\WorkTimes\WorkTimeType;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use App\Models\WorkTime;
use App\Settings\FrontendSetting;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class BookingCalculator
{
    private Collection $rooms;

    private FrontendSetting $settings;

    public function __construct(
        private CarbonImmutable $start,
        private CarbonImmutable $end,
        private AppointmentType $appointmentType,
        private Branch $branch,
        private Category $category,
        private Collection $providers,
        private int $duration,
    ) {
        $this->rooms = $this->getAvailableRooms();
        $this->settings = app(FrontendSetting::class);

        if ($this->duration < $this->settings->min_duration) {
            $this->duration = $this->settings->min_duration;
        }
    }

    public static function make(
        string|CarbonInterface $start,
        string|CarbonInterface $end,
        string|AppointmentType $appointmentType,
        null|string|int|Branch $branch,
        null|string|int|Category $category,
        array|Collection $services,
        null|array|Collection $providers = null,
        ?int $duration = null,
    ): self {
        // Convert the Start Date
        if (is_string($start)) {
            $start = CarbonImmutable::parse($start);
        }
        // Convert the End Date
        if (is_string($end)) {
            $end = CarbonImmutable::parse($end);
        }

        if (is_string($appointmentType)) {
            $appointmentType = AppointmentType::from($appointmentType);
        }

        // Convert the Branch
        if (is_string($branch) || is_int($branch)) {
            $branch = Branch::find($branch);
        } elseif (is_null($branch)) {
            $branch = Branch::first();
        }

        // Convert the Category
        if (is_string($category) || is_int($category)) {
            $category = Category::find($category);
        } elseif (is_null($category)) {
            $category = Category::first();
        }

        // Convert the Services
        if (is_array($services)) {
            $services = Service::query()
                ->whereIn('id', $services)
                ->get();
        }

        // Convert the Providers
        if (is_array($providers)) {
            /** @var Collection $providers */
            $providers = User::query()
                ->whereIn('id', $providers)
                ->get();
        } elseif (is_null($providers)) {
            $providers = FindAvailableProviders::make($appointmentType, $branch, $category, $services)->execute();
        }

        if (is_null($duration)) {
            $duration = CalculateDuration::make($appointmentType, $services)->execute();
        }

        return new self(
            start: $start->toImmutable(),
            end: $end->toImmutable(),
            appointmentType: $appointmentType,
            branch: $branch,
            category: $category,
            providers: $providers,
            duration: $duration,
        );
    }

    public function openOptions(): array
    {
        return $this->openSlots()
            ->map(fn (array $slot) => $this->slotToOptions($slot))
            ->flatten(1)
            ->sortBy('start')
            ->mapWithKeys(fn (array $item) => [
                $item['start_time'] => $item['title'],
            ])
            ->toArray();
    }

    public function openList(): array
    {
        return $this->openSlots()
            ->map(fn (array $slot) => $this->slotToOptions($slot))
            ->flatten(1)
            ->map(function (array $item) {
                $item['groupId'] = 'open';
                $item['display'] = 'auto';

                return $item;
            })
            ->toArray();
    }

    public function openSlots(): Collection
    {
        return $this->rooms->map(fn (Room $room) => $this->openSlotsForRoom($room))->flatten(1);
    }

    private function slotToOptions(array $slot): array
    {
        $options = collect();
        $period = CarbonInterval::minutes($this->settings->slot_step)->toPeriod($slot['start'], $slot['end']);

        foreach ($period as $date) {
            if ($date->copy()->addMinutes($this->duration)->gt($slot['end'])) {
                continue;
            }

            $options->push([
                'start' => $date->toISOString(),
                'end' => $date->copy()->addMinutes($this->duration)->toIsoString(),
                'start_time' => $date->format('H:i'),
                'end_time' => $date->copy()->addMinutes($this->duration)->format('H:i'),
                'title' => sprintf('%s-%s', $date->format('H:i'), $date->copy()->addMinutes($this->duration)->format('H:i')),
            ]);
        }

        return $options->toArray();
    }

    private function openSlotsForRoom(Room $room): Collection
    {
        $openSlots = collect();

        $startOpen = $this->start;

        $this->closedSlotsForRoom($room)
            ->each(function (array $slot) use ($room, &$startOpen, &$openSlots) {
                if ($startOpen->gt($slot['start'])) {
                    if ($startOpen->lt($slot['end'])) {
                        $startOpen = $slot['end'];
                    }

                    return;
                }

                if ($startOpen->diffInMinutes($slot['start']) < $this->duration) {
                    $startOpen = $slot['end'];

                    return;
                }

                //TODO: Resource Check
                $openSlots->push([
                    'start' => $startOpen,
                    'end' => $slot['start'],
                ]);

                $startOpen = $slot['end'];
            });

        return $openSlots;
    }

    private function closedSlotsForRoom(Room $room): Collection
    {
        return collect()
            ->merge($room->appointments->map(fn (Appointment $appointment) => [
                'start' => $appointment->start,
                'end' => $appointment->end,
            ]))
            ->merge($this->closedTimesForRoom($room))
            ->sortBy('start');
    }

    private function closedTimesForRoom(Room $room): Collection
    {
        if ($room->workTimes->isEmpty()) {
            return collect([[
                'start' => $this->start,
                'end' => $this->end,
            ]]);
        }

        $closed = collect();

        $startClosed = $this->start;

        $room->workTimes->each(function (WorkTime $wt) use (&$startClosed, $closed) {
            if ($startClosed->lt($wt->start)) {
                $closed->push([
                    'start' => $startClosed,
                    'end' => $wt->start,
                ]);
            }
            $startClosed = $wt->end;
        });

        // Mark the rest time as closed
        $closed->push([
            'start' => $startClosed,
            'end' => $this->end,
        ]);

        return $closed;
    }

    private function getAvailableRooms(): Collection
    {
        return $this->branch
            ->rooms()
            ->with([
                'workTimes' => fn (HasMany $query) => $query
                    ->where('start', '<=', $this->end)
                    ->where('end', '>=', $this->start)
                    ->where('type', WorkTimeType::Provider)
                    ->whereIn('user_id', $this->providers->pluck('id')->toArray())
                    ->orderBy('start'),
                'appointments' => fn (HasMany $query) => $query
                    ->where('start', '<=', $this->end)
                    ->where('end', '>=', $this->start)
                    ->orderBy('start'),
            ])
            ->whereHas("workTimes", fn(Builder $query) => $query
                ->where('start', '<=', $this->end)
                ->where('end', '>=', $this->start)
                ->where('type', WorkTimeType::Provider)
                ->whereIn('user_id', $this->providers->pluck('id')->toArray())
            )
            ->get();
    }
}
