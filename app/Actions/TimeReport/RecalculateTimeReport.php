<?php

namespace App\Actions\TimeReport;

use App\Enums\TimeRecords\LeaveType;
use App\Enums\TimeRecords\TimeCheckStatus;
use App\Enums\TimeRecords\TimeConstraint;
use App\Enums\User\WageType;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\TimeReport;
use Illuminate\Database\Eloquent\Model;

class RecalculateTimeReport
{
    private bool $save_quietly;

    private bool $skip_save;

    private ?Availability $availability;

    public function __construct(
        private TimeReport $report
    ) {
        $this->availability = FindUserAvailabilityForDate::make($report->user, $report->date)->execute();
        $this->save_quietly = false;
        $this->skip_save = false;
    }

    public static function make(TimeReport $report): self
    {
        return new self($report);
    }

    public function saveQuietly(): self
    {
        $this->save_quietly = true;

        return $this;
    }

    public function skipSave(): self
    {
        $this->skip_save = true;

        return $this;
    }

    public function excecute(): TimeReport
    {
        if ($this->report->date->gte(today())) {
        }

        $this->recalculateTargetMinutes();
        $this->recalculateAvailability();
        //$this->recalculateWorkTime();
        $this->recalculateLeaveType();
        $this->recalculateTime();

        if ($this->skip_save) {
            return $this->report;
        }

        if ($this->save_quietly) {
            $this->report->saveQuietly();
        } else {
            $this->report->save();
        }

        return $this->report;
    }

    private function recalculateTargetMinutes(): void
    {
        if (is_null($this->availability)) {
            return;
        }

        $this->report->target_minutes = $this->availability?->getRecordsForDate($this->report->date->toImmutable())?->sum('target_minutes') ?? 0;
    }

    private function recalculateAvailability(): void
    {
        $availabilities = $this->availability?->getRecordsForDate($this->report->date->toImmutable()) ?? collect();

        if ($availabilities->isEmpty()) {
            return;
        }

        $start = $availabilities->sortBy('start')->first()?->start;
        $end = $availabilities->sortBy('start')->first()?->end;
        $this->report->availability_start = !empty($start) ? $this->report->date->copy()->setTimeFromTimeString($start) : null;
        $this->report->availability_end = !empty($end) ? $this->report->date->copy()->setTimeFromTimeString($end) : null;
        $this->report->availability_minutes = $availabilities->sum('target_minutes');
    }

    private function recalculateWorkTime(): void
    {
        // Only recalculate if report is in the future
        if ($this->report->date->lt(today())) {
            return;
        }

        $workTimes = $this->report->user->workTimes()
            ->where('start', '>=', $this->report->date->startOfDay())
            ->where('start', '<=', $this->report->date->endOfDay())
            ->get();

        if ($workTimes->isEmpty()) {
            return;
        }

        $appointments = $workTimes->pluck('appointments')->flatten();
        if($appointments->isNotEmpty()) {
            $this->report->appointment_start = $appointments->sortBy('start')->first()->start;
            $this->report->appointment_end = $appointments->sortByDesc('end')->first()->end;
            $this->report->appointment_minutes = $appointments->sum(fn (Appointment $appointment) => $appointment->start->diffInMinutes($appointment->end));
        }

        $this->report->work_time_start = $workTimes->sortBy('start')->first()->start;
        $this->report->work_time_end = $workTimes->sortByDesc('end')->first()->end;

        if (is_null($this->report->work_time_start) || is_null($this->report->work_time_end)) {
            return;
        }

        $this->report->work_time_minutes = $this->report->work_time_start->diffInMinutes($this->report->work_time_end);
    }

    private function recalculateTime(): void
    {
        if ($this->report->date->gt(today())) {
            return;
        }

        if (isset($this->report->time_in)) {
            $this->report->time_in_status = $this->getTimeInStatus();
        }

        $this->report = AutocheckoutTimeReport::make($this->report)->skipSave()->execute();

        if (isset($this->report->time_out)) {
            $this->report->time_out_status = $this->getTimeOutStatus();
        }

        if (isset($this->report->time_in) && isset($this->report->time_out)) {
            $this->report->total_minutes = $this->report->time_in->diffInMinutes($this->report->time_out);
        }

        $this->report->break_minutes = $this->getBreakMinutes();

        $this->report->actual_minutes = $this->report->total_minutes - $this->report->break_minutes;
        $this->report->actual_minutes += $this->report->manual_minutes ?? 0;

        if (isset($this->report->leave_type)) {
            if ($this->report->leave_type->getTargetFulfilled()) {
                $this->report->actual_minutes += $this->report->target_minutes;
            }
        }

        $this->report->uncapped_overtime_minutes = $this->getUncappedOvertime();
        $this->report->overtime_minutes = $this->getOvertime();
    }

    private function recalculateLeaveType(): void
    {
        $leave = $this->report->user->leaves()
            ->approved()
            ->where('from', '<=', $this->report->date)
            ->where('till', '>=', $this->report->date)
            ->first();

        if (! is_null($leave) && $this->isLeaveDay()) {
            $this->report->leave_type = $leave->leave_type;

            return;
        }

        if ($this->report->date->isHoliday()) {
            $this->report->leave_type = LeaveType::Holiday;

            return;
        }

        $this->report->leave_type = null;
    }

    private function isLeaveDay(): bool
    {
        $minutes = $this->report->target_minutes;
        $date = $this->report->date;

        return $date->isExtraWorkday() || (($date->isWeekday() || $minutes > 0) && ! $date->isHoliday());
    }

    private function getUncappedOvertime(): int
    {
        if($this->availability?->availabilityUserOption?->wage_type == WageType::Hourly) {
            return $this->report->actual_minutes;
        }

        return $this->report->actual_minutes - $this->report->target_minutes;
    }

    private function getOvertime():int
    {
        if($this->availability?->availabilityUserOption?->wage_type == WageType::Hourly) {
            $target = $this->report->target_minutes;
            if ($this->report->is_overtime_capped && $this->report->uncapped_overtime_minutes > ($target + timeSettings()->overtime_cap_minutes)) {
                return $target + timeSettings()->overtime_cap_minutes;
            }

            return $this->report->uncapped_overtime_minutes;
        }

        if ($this->report->is_overtime_capped && $this->report->uncapped_overtime_minutes > timeSettings()->overtime_cap_minutes) {
            return timeSettings()->overtime_cap_minutes;
        }

        return $this->report->uncapped_overtime_minutes;
    }

    private function getTimeInStatus(): TimeCheckStatus
    {
        if (is_null($this->report->availability_start)) {
            return TimeCheckStatus::Ok;
        }

        // Check if the TimeIn was early
        if (
            $this->report->time_in
                ->subMinutes(timeSettings()->early_check_in_minutes)
                ->lt($this->report->availability_start)
        ) {
            return TimeCheckStatus::Early;
        }

        // Check if the TimeIn was late
        if (
            $this->report->time_in
                ->addMinutes(timeSettings()->late_check_in_minutes)
                ->gt($this->report->availability_start)
        ) {
            return TimeCheckStatus::Late;
        }

        return TimeCheckStatus::Ok;
    }

    private function getTimeOutStatus(): TimeCheckStatus
    {
        if ($this->report->time_out_status == TimeCheckStatus::Automatic) {
            return TimeCheckStatus::Automatic;
        }

        if (is_null($this->report->availability_end)) {
            return TimeCheckStatus::Ok;
        }

        // Check if the TimeOut was early
        if (
            $this->report->time_out
                ->subMinutes(timeSettings()->early_check_out_minutes)
                ->lt($this->report->availability_end)
        ) {
            return TimeCheckStatus::Early;
        }

        // Check if the TimeOut was late
        if (
            $this->report->time_out
                ->addMinutes(timeSettings()->late_check_out_minutes)
                ->gt($this->report->availability_end)
        ) {
            return TimeCheckStatus::Late;
        }

        return TimeCheckStatus::Ok;
    }

    private function getBreakMinutes(): int
    {
        $hours = $this->report->total_minutes / 60;

        if ($hours < 2) {
            return 0; // TODO: Add under two hour break setting
        } elseif ($hours < 4) {
            return timeSettings()->two_hour_break;
        } elseif ($hours < 6) {
            return timeSettings()->four_hour_break;
        } elseif ($hours < 8) {
            return timeSettings()->six_hour_break;
        } elseif ($hours < 10) {
            return timeSettings()->eight_hour_break;
        } else {
            return timeSettings()->ten_hour_break;
        }
    }
}
