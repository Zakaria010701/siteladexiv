<?php

namespace App\Actions\TimeReport;

use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class UpdateTimeReportOverviewRange
{
    public function __construct(
        private CarbonImmutable $start,
        private CarbonImmutable $end,
        private User $user,
    ) {}

    public static function make(CarbonInterface $start, CarbonInterface $end, User $user): self
    {
        return new self($start->toImmutable(), $end->toImmutable(), $user);
    }

    public function excecute()
    {
        $period = CarbonPeriod::create($this->start->startOfMonth(), '1 month', $this->end->endOfMonth());
    }
}
