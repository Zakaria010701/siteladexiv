<?php

namespace App\Actions\TimeReport;

use App\Models\Availability;
use App\Models\TimePlan;
use App\Models\TimeReport;
use App\Models\TimeReportOverview;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class GenerateTimeReport
{
    private bool $save_quietly;
    private ?Availability $availability;

    public function __construct(
        private readonly CarbonImmutable $date,
        private readonly User $user,
        private readonly TimeReportOverview $overview,
    ) {
        $this->availability = FindUserAvailabilityForDate::make($user, $date)->execute();
        $this->save_quietly = false;
    }

    public static function make(CarbonInterface $date, User $user, ?TimeReportOverview $overview = null): self
    {
        $date = $date->toImmutable();
        if (is_null($overview)) {
            $overview = GenerateTimeReportOverview::make($date->startOfMonth(), $user)->execute();
        }

        return new self($date, $user, $overview);
    }

    public function saveQuietly(): self
    {
        $this->save_quietly = true;

        return $this;
    }

    public function execute(): TimeReport
    {
        if ($this->overview->timeReports()->where('date', $this->date->format('Y-m-d'))->exists()) {
            return $this->overview->timeReports()->where('date', $this->date->format('Y-m-d'))->first();
        }

        $availabilities = $this->availability?->getRecordsForDate($this->date) ?? collect();

        $availabilityStart = null;
        $availabilityEnd = null;
        if ($availabilities->isNotEmpty()) {
            $start = $availabilities->sortBy('start')->first()?->start;
            $end = $availabilities->sortBy('start')->first()?->end;
            $availabilityStart = !empty($start) ? $this->date->copy()->setTimeFromTimeString($start) : null;
            $availabilityEnd = !empty($end) ? $this->date->copy()->setTimeFromTimeString($end) : null;
        }

        //$appointments = $availabilities->pluck('appointments')->flatten();
        $appointmentStart = null;
        $appointmentEnd = null;
        /*if($appointments->isNotEmpty()) {
            $appointmentStart = $appointments->sortBy('start')->first()->start;
            $appointmentEnd = $appointments->sortByDesc('end')->first()->end;
        }*/

        $attributes = [
            'date' => $this->date->format('Y-m-d'),
            'user_id' => $this->user->id,
            'target_minutes' => $this->getTargetMinutes(),
            'availability_start' => $availabilityStart,
            'availability_end' => $availabilityEnd,
            'availability_minutes' => $availabilities->sum('target_minutes') ?? 0,
            'appointment_start' => $appointmentStart,
            'appointment_end' => $appointmentEnd,
            'is_overtime_capped' => timeSettings()->overtime_cap_enabled,
        ];

        if ($this->save_quietly) {
            return $this->overview->timeReports()->createQuietly($attributes);
        }

        return $this->overview->timeReports()->create($attributes);
    }

    private function getTargetMinutes(): int
    {
        $availabilities = $this->availability?->getRecordsForDate($this->date)  ?? collect();
        if ($availabilities->isEmpty()) {
            return 0;
        }

        return $availabilities->sum('target_minutes') ?? 0;
    }
}
