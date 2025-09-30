<?php

namespace App\Actions\TimeReport;

use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class FindTotalLeaveDaysBetween
{
    public function __construct(
        private readonly User $user,
        private readonly CarbonImmutable $from,
        private readonly CarbonImmutable $till,
    ) {}

    public static function make(User $user, CarbonInterface $from, CarbonInterface $till): self
    {
        return new self($user, $from->toImmutable(), $till->toImmutable());
    }

    public function execute(): int
    {
        return $this->from->setBusinessDayChecker(function (CarbonInterface $date) {
            $timePlan = FindTimePlanForDate::make($this->user, $date)->execute();
            $minutes = $timePlan->getTargetMinutes($date);

            return $date->isExtraWorkday() || (($date->isWeekday() || $minutes > 0) && ! $date->isHoliday());
        })->diffInBusinessDays($this->till->addDay());
    }
}
