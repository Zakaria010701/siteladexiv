<?php

namespace App\Observers;

use App\Actions\TimeReport\UpdateTimeReportRange;
use App\Models\TimePlan;

class TimePlanObserver
{
    /**
     * Handle the TimePlan "created" event.
     */
    public function created(TimePlan $timePlan): void
    {
        UpdateTimeReportRange::make($timePlan->start_date, $timePlan->end_date ?? today(), $timePlan->user)->excecute();
    }

    /**
     * Handle the TimePlan "updated" event.
     */
    public function updated(TimePlan $timePlan): void
    {
        UpdateTimeReportRange::make($timePlan->start_date, $timePlan->end_date ?? today(), $timePlan->user)->excecute();
    }

    /**
     * Handle the TimePlan "deleted" event.
     */
    public function deleted(TimePlan $timePlan): void
    {
        UpdateTimeReportRange::make($timePlan->start_date, $timePlan->end_date ?? today(), $timePlan->user)->excecute();
    }

    /**
     * Handle the TimePlan "restored" event.
     */
    public function restored(TimePlan $timePlan): void
    {
        UpdateTimeReportRange::make($timePlan->start_date, $timePlan->end_date ?? today(), $timePlan->user)->excecute();
    }

    /**
     * Handle the TimePlan "force deleted" event.
     */
    public function forceDeleted(TimePlan $timePlan): void
    {
        UpdateTimeReportRange::make($timePlan->start_date, $timePlan->end_date ?? today(), $timePlan->user)->excecute();
    }
}
