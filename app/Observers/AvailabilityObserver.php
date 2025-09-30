<?php

namespace App\Observers;

use App\Actions\TimeReport\UpdateTimeReportRange;
use App\Models\Availability;

class AvailabilityObserver
{
    /**
     * Handle the Availability "created" event.
     */
    public function created(Availability $availability): void
    {
        $availability->updateTimeReport();
    }

    /**
     * Handle the Availability "updated" event.
     */
    public function updated(Availability $availability): void
    {
        $availability->updateTimeReport();
    }

    /**
     * Handle the Availability "deleted" event.
     */
    public function deleted(Availability $availability): void
    {
        $availability->updateTimeReport();
    }

    /**
     * Handle the Availability "restored" event.
     */
    public function restored(Availability $availability): void
    {
        $availability->updateTimeReport();
    }

    /**
     * Handle the Availability "force deleted" event.
     */
    public function forceDeleted(Availability $availability): void
    {
        $availability->updateTimeReport();
    }
}
