<?php

namespace App\Observers;

use App\Actions\TimeReport\RecalculateTimeReportOverview;
use App\Actions\TimeReport\RegeneratePayroll;
use App\Models\Payroll;

class PayrollObserver
{
    /**
     * Handle the Payroll "created" event.
     */
    public function created(Payroll $payroll): void
    {
        $overview = $payroll->user->timeReportOverviews()
            ->whereMonth('date', $payroll->till->month)
            ->whereYear('date', $payroll->till->year)
            ->first();
        if (isset($overview)) {
            RecalculateTimeReportOverview::make($overview)->excecute();
        }

        /** @var Payroll $next */
        $next = $payroll->user->payrolls()->where('till', '>', $payroll->till)->first();

        if (isset($next)) {
            RegeneratePayroll::make($next)->execute();
        }
    }

    /**
     * Handle the Payroll "updated" event.
     */
    public function updated(Payroll $payroll): void
    {
        if (isset($payroll->next)) {
            RegeneratePayroll::make($payroll->next)->execute();
        }
    }

    /**
     * Handle the Payroll "deleted" event.
     */
    public function deleted(Payroll $payroll): void
    {
        if (isset($payroll->next)) {
            RegeneratePayroll::make($payroll->next)->execute();
        }
    }

    /**
     * Handle the Payroll "restored" event.
     */
    public function restored(Payroll $payroll): void
    {
        //
    }

    /**
     * Handle the Payroll "force deleted" event.
     */
    public function forceDeleted(Payroll $payroll): void
    {
        //
    }
}
