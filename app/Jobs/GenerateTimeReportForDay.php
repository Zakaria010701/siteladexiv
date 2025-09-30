<?php

namespace App\Jobs;

use App\Actions\TimeReport\GenerateTimeReport;
use App\Actions\TimeReport\RecalculateTimeReport;
use App\Jobs\Time\UpdateTimeReportOverviewForRange;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class GenerateTimeReportForDay implements ShouldQueue
{
    use Batchable;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private ?CarbonImmutable $date = null)
    {
        if (is_null($this->date)) {
            $this->date = today()->toImmutable();
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $batch = User::query()
            ->get()
            ->map(fn (User $user) => new UpdateTimeReportOverviewForRange($this->date, $this->date, $this->date, $user))
            ->toArray();

        Bus::batch($batch)->dispatch();
    }
}
