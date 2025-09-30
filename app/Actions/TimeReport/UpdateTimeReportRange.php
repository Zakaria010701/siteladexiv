<?php

namespace App\Actions\TimeReport;

use App\Jobs\Time\UpdateTimeReportOverviewForRange;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class UpdateTimeReportRange
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
        $period = CarbonPeriod::create($this->start->startOfMonth(), '1 month', today()->endOfMonth());

        $chain = [];

        foreach ($period as $date) {
            $chain[] = Bus::batch([
                new UpdateTimeReportOverviewForRange($date->toImmutable(), $this->start, today()->toImmutable(), $this->user),
            ])->then(function (Batch $batch) {
                Log::debug(__('Finished Update of TimeReport'));
            })->name(__('Update TimeReport of :name for :date', [
                'name' => $this->user->lastname,
                'date' => $date->format('Y-m-d'),
            ]));
        }

        Bus::chain($chain)->dispatch();
    }
}
