<?php

namespace App\Actions\TimeReport;

use App\Enums\TimeRecords\LeaveType;
use App\Models\TimePlan;
use App\Models\TimeReportOverview;
use Carbon\CarbonInterface;

class RecalculateTimeReportOverview
{
    private bool $save_quietly;

    private ?TimePlan $timePlan;

    public function __construct(
        private TimeReportOverview $overview
    ) {
        $this->timePlan = FindTimePlanForDate::make($overview->user, $overview->date)->execute();
        $this->save_quietly = false;

    }

    public static function make(TimeReportOverview $overview): self
    {
        return new self($overview);
    }

    public function saveQuietly(): self
    {
        $this->save_quietly = true;

        return $this;
    }

    public function excecute(): TimeReportOverview
    {
        $payroll = $this->overview->user->payrolls()
            ->whereMonth('till', $this->overview->date->month)
            ->whereYear('till', $this->overview->date->year)
            ->latest('till')
            ->first();

        if (isset($payroll)) {
            $reports = $this->overview->timeReports()
                ->where('date', '>', $payroll->till)
                ->get();
        } else {
            $reports = $this->overview->timeReports;
        }

        $previous = $this->overview->previous;
        if ($previous === null) {
            $previous = $this->overview->user->timeReportOverviews()
                ->whereMonth('date', $this->overview->date->subMonth()->month)
                ->whereYear('date', $this->overview->date->subMonth()->year)
                ->first();

            $this->overview->previous_id = $previous->id ?? null;
        }

        $this->overview->target_minutes = $reports->sum('target_minutes');
        $this->overview->availability_minutes = $reports->sum('availability_minutes');
        $this->overview->total_minutes = $reports->sum('total_minutes');
        $this->overview->real_total_minutes = $reports->sum('real_total_minutes');
        $this->overview->actual_minutes = $reports->sum('actual_minutes');
        $this->overview->overtime_minutes = $reports->sum('overtime_minutes');
        $this->overview->uncapped_overtime_minutes = $reports->sum('uncapped_overtime_minutes');
        $this->overview->carry_overtime_minutes = isset($payroll) ? 0 : $previous?->total_overtime ?? 0;

        $this->overview->leave_days = $this->overview->timeReports()->whereNotNull('leave_type')->count();
        $this->overview->sick_days = $this->overview->timeReports()->where('leave_type', LeaveType::SickLeave)->count();
        $this->overview->vacation_days = $this->overview->timeReports()->where('leave_type', LeaveType::Vacation)->count();

        $carry_vacation_days = $previous?->total_vacation ?? 0;

        if (isset($this->timePlan)) {
            if ($this->timePlan->start_date->startOfMonth()->eq($this->overview->date->startOfMonth())) {
                $carry_vacation_days += $this->timePlan->start_vacation_days;
            } elseif ($this->overview->date->month == CarbonInterface::JANUARY) {
                $carry_vacation_days += $this->timePlan->yearly_vacation_days;
            }
        }

        $this->overview->carry_vacation_days = $carry_vacation_days;

        if ($this->save_quietly) {
            $this->overview->saveQuietly();
        } else {
            $this->overview->save();
        }

        return $this->overview;
    }
}
