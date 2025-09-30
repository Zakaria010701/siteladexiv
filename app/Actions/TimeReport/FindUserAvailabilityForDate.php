<?php

namespace App\Actions\TimeReport;

use App\Models\Availability;
use App\Models\TimePlan;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class FindUserAvailabilityForDate
{
    public function __construct(
        private readonly User $user,
        private readonly CarbonImmutable $date,
    ) {}

    public static function make(User $user, CarbonInterface $date): self
    {
        return new self($user, $date->toImmutable());
    }

    public function execute(): ?Availability
    {
        return $this->user->availabilities()
            ->where('start_date', '<=', $this->date)
            ->where(fn (Builder $query) => $query
                ->whereNull('end_date')
                ->orWhere('end_date', '>=', $this->date)
            )
            ->orderByDesc('start_date')
            ->first();
    }
}
