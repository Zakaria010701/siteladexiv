<?php

namespace App\Actions\TimeReport;

use App\Models\TimeReportOverview;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class GenerateTimeReportOverview
{
    private bool $save_quietly;

    public function __construct(
        private readonly CarbonImmutable $date,
        private readonly User $user,
    ) {
        $this->save_quietly = false;
    }

    public static function make(CarbonInterface $date, User $user): self
    {
        return new self($date->toImmutable(), $user);
    }

    public function saveQuietly(): self
    {
        $this->save_quietly = true;

        return $this;
    }

    public function execute(): TimeReportOverview
    {
        if (
            $this->user->timeReportOverviews()
                ->whereMonth('date', $this->date->month)
                ->whereYear('date', $this->date->year)
                ->exists()
        ) {
            return $this->user->timeReportOverviews()
                ->whereMonth('date', $this->date->month)
                ->whereYear('date', $this->date->year)
                ->first();
        }

        $prevOverview = $this->user->timeReportOverviews()
            ->whereMonth('date', $this->date->subMonth()->month)
            ->whereYear('date', $this->date->subMonth()->year)
            ->first();

        $attributes = [
            'date' => $this->date->startOfMonth()->format('Y-m-d'),
            'previous_id' => $prevOverview->id ?? null,
            'carry_overtime_minutes' => $prevOverview?->overtime_minutes ?? 0,
            'carry_vacation_days' => $prevOverview?->vacation_days ?? 0,
        ];

        if ($this->save_quietly) {
            return $this->user->timeReportOverviews()->createQuietly($attributes);
        }

        return $this->user->timeReportOverviews()->create($attributes);
    }
}
