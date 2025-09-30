<?php

namespace App\Jobs;

use App\Actions\TimeReport\AutocheckoutTimeReport;
use App\Models\TimeReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AutocheckoutTimeReports implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        TimeReport::query()
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->get()
            ->each(function (TimeReport $timeReport) {
                AutocheckoutTimeReport::make($timeReport)->execute();
            });
    }
}
