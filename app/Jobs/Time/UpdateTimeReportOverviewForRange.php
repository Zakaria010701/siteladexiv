<?php

namespace App\Jobs\Time;

use App\Actions\TimeReport\GenerateTimeReportOverview;
use App\Models\TimeReport;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateTimeReportOverviewForRange implements ShouldQueue
{
    use Batchable;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private CarbonImmutable $date,
        private CarbonImmutable $start,
        private CarbonImmutable $end,
        private User $user,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $overview = GenerateTimeReportOverview::make($this->date, $this->user)->execute();

        $chain = $overview->timeReports()
            ->where('date', '>=', $this->start)
            ->where('date', '<=', $this->end)
            ->get()
            ->map(function (TimeReport $report) {
                return new UpdateTimeReport($report);
            })
            ->toArray();

        $chain[] = new UpdateTimeReportOverview($overview);

        $this->batch()->add([
            $chain,
        ]);
    }
}
