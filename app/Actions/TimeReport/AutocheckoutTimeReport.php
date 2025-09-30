<?php

namespace App\Actions\TimeReport;

use App\Enums\TimeRecords\TimeConstraint;
use App\Models\TimeReport;
use Illuminate\Support\Carbon;

class AutocheckoutTimeReport
{
    private bool $skip_save;

    public function __construct(
        private TimeReport $report,
    ) {
        $this->skip_save = false;
    }

    public static function make(TimeReport $report): self
    {
        return new self($report);
    }

    public function skipSave(): self
    {
        $this->skip_save = true;

        return $this;
    }

    public function execute(): TimeReport
    {
        if (isset($this->report->time_out)) {
            return $this->report;
        }

        if (is_null($this->report->time_in)) {
            return $this->report;
        }

        $checkoutTime = $this->report->time_in->addMinute();

        if (! $checkoutTime->copy()->endOfDay()->lte(now())) {
            return $this->report;
        }

        $this->report->time_out = $checkoutTime;
        $this->report->real_time_out = $checkoutTime;

        if ($this->skip_save) {
            return $this->report;
        }

        $this->report->save();

        return $this->report;
    }

    private function getCheckoutTimeForTarget() : Carbon|null
    {
        return $this->report->time_in->addMinutes($this->report->target_minutes)->addMinutes($this->timeConstraint->getAutologoutAfterMinutes());
    }

    private function getCheckoutTimeForWorktime() : Carbon|null
    {
        $end = $this->report->appointment_end ?? $this->report->work_time_end;
        return $end->addMinutes($this->timeConstraint->getAutologoutAfterMinutes());
    }
}
