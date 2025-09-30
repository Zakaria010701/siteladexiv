<?php

namespace App\Observers;

use App\Actions\TimeReport\UpdateTimeReportRange;
use App\Models\Leave;

class LeaveObserver
{
    /**
     * Handle the Leave "created" event.
     */
    public function created(Leave $leave): void
    {
        UpdateTimeReportRange::make($leave->from, $leave->till ?? today(), $leave->user)->excecute();
    }

    /**
     * Handle the Leave "updated" event.
     */
    public function updated(Leave $leave): void
    {
        UpdateTimeReportRange::make($leave->from, $leave->till ?? today(), $leave->user)->excecute();
    }

    /**
     * Handle the Leave "deleted" event.
     */
    public function deleted(Leave $leave): void
    {
        UpdateTimeReportRange::make($leave->from, $leave->till ?? today(), $leave->user)->excecute();
    }

    /**
     * Handle the Leave "restored" event.
     */
    public function restored(Leave $leave): void
    {
        UpdateTimeReportRange::make($leave->from, $leave->till ?? today(), $leave->user)->excecute();
    }

    /**
     * Handle the Leave "force deleted" event.
     */
    public function forceDeleted(Leave $leave): void
    {
        UpdateTimeReportRange::make($leave->from, $leave->till ?? today(), $leave->user)->excecute();
    }
}
