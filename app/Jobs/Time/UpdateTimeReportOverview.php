<?php

namespace App\Jobs\Time;

use App\Actions\TimeReport\RecalculateTimeReportOverview;
use App\Models\TimeReportOverview;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateTimeReportOverview implements ShouldQueue
{
    use Batchable;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private TimeReportOverview $overview,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        RecalculateTimeReportOverview::make($this->overview)->saveQuietly()->excecute();
    }
}
