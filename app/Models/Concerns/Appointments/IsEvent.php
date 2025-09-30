<?php

namespace App\Models\Concerns\Appointments;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait IsEvent
{
    public function scopeTimeFrame(Builder $query, Branch $branch, Carbon $start, Carbon $end): void
    {
        $query->with([

        ])
            ->where('branch_id', $branch->id)
            ->whereBetween('start', [$start, $end]);
    }
}
