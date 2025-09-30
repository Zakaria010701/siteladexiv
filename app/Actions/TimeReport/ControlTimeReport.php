<?php

namespace App\Actions\TimeReport;

use App\Models\TimeReport;
use App\Models\User;

class ControlTimeReport
{
    public function __construct(
        private TimeReport $report,
        private User $processor,
    ) {}

    public static function make(TimeReport $report, User $processor): self
    {
        return new self($report, $processor);
    }

    public function execute(): TimeReport
    {
        $this->report->controlled_at = now();
        $this->report->controlled_by_id = $this->processor->id;
        $this->report->save();

        return $this->report;
    }
}
