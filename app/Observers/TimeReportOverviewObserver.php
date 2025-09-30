<?php

namespace App\Observers;

use App\Actions\TimeReport\GenerateTimeReport;
use App\Actions\TimeReport\RecalculateTimeReportOverview;
use App\Models\TimeReportOverview;

class TimeReportOverviewObserver
{
    /**
     * Handle the TimeReportOverview "created" event.
     */
    public function created(TimeReportOverview $timeReportOverview): void
    {
        $period = $timeReportOverview->date
            ->startOfMonth()
            ->diff($timeReportOverview->date->endOfMonth())
            ->stepBy('day');

        foreach ($period as $date) {
            GenerateTimeReport::make($date, $timeReportOverview->user, $timeReportOverview)->execute();
        }
    }

    /**
     * Handle the TimeReportOverview "updated" event.
     */
    public function updated(TimeReportOverview $timeReportOverview): void
    {
        if (! isset($timeReportOverview->next)) {
            return;
        }

        RecalculateTimeReportOverview::make($timeReportOverview->next)->excecute();
    }

    /**
     * Handle the TimeReportOverview "deleted" event.
     */
    public function deleted(TimeReportOverview $timeReportOverview): void
    {
        //
    }

    /**
     * Handle the TimeReportOverview "restored" event.
     */
    public function restored(TimeReportOverview $timeReportOverview): void
    {
        //
    }

    /**
     * Handle the TimeReportOverview "force deleted" event.
     */
    public function forceDeleted(TimeReportOverview $timeReportOverview): void
    {
        //
    }
}
