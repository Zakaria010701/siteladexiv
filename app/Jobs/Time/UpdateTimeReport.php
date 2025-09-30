<?php

namespace App\Jobs\Time;

use App\Actions\TimeReport\RecalculateTimeReport;
use App\Models\TimeReport;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateTimeReport implements ShouldQueue
{
    use Batchable;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public TimeReport $report
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        RecalculateTimeReport::make($this->report)->saveQuietly()->excecute();
    }
}
