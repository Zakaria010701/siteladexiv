<?php

namespace App\Observers;

use App\Actions\TimeReport\RecalculateTimeReportOverview;
use App\Models\TimeReport;

class TimeReportObserver
{
    /**
     * Handle the TimeReport "created" event.
     */
    public function created(TimeReport $timeReport): void
    {
        //
    }

    /**
     * Handle the TimeReport "updated" event.
     */
    public function updated(TimeReport $timeReport): void
    {
        if (isset($timeReport->leave_type)) {
            $leave = $timeReport->user->leaves()
                ->where('from', '<=', $timeReport->date)
                ->where('till', '>=', $timeReport->date)
                ->first();

            if (is_null($leave)) {
                $timeReport->user->leaves()
                    ->createQuietly([
                        'leave_type' => $timeReport->leave_type,
                        'from' => $timeReport->date,
                        'till' => $timeReport->date,
                        'total_leave_days' => 1,
                        'processed_by_id' => auth()->user()->can('admin_leave') ? auth()->id() : null,
                        'approved_at' => auth()->user()->can('admin_leave') ? now() : null,
                        'admin_note' => __('Automaticaly created from time report'),
                        'meta' => [
                            'auto_created' => true,
                            'time_report_id' => $timeReport->id,
                            'causer_id' => auth()->id(),
                        ],
                    ]);
            }
        }

        RecalculateTimeReportOverview::make($timeReport->timeReportOverview)->excecute();
    }

    /**
     * Handle the TimeReport "deleted" event.
     */
    public function deleted(TimeReport $timeReport): void
    {
        RecalculateTimeReportOverview::make($timeReport->timeReportOverview)->excecute();
    }

    /**
     * Handle the TimeReport "restored" event.
     */
    public function restored(TimeReport $timeReport): void
    {
        RecalculateTimeReportOverview::make($timeReport->timeReportOverview)->excecute();
    }

    /**
     * Handle the TimeReport "force deleted" event.
     */
    public function forceDeleted(TimeReport $timeReport): void
    {
        RecalculateTimeReportOverview::make($timeReport->timeReportOverview)->excecute();
    }
}
