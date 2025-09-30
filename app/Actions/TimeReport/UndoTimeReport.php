<?php

namespace App\Actions\TimeReport;

use App\Models\TimeReport;

class UndoTimeReport
{
    public function __construct(
        private TimeReport $report,
    ) {}

    public static function make(TimeReport $report): self
    {
        return new self($report);
    }

    public function execute(): TimeReport
    {
        $this->report->time_in = $this->report->real_time_in;
        $this->report->time_out = $this->report->real_time_out;
        $this->report->save();

        return $this->report;
    }
}
