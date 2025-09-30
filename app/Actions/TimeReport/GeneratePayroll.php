<?php

namespace App\Actions\TimeReport;

use App\Models\Payroll;
use App\Models\TimeReport;
use App\Models\User;

class GeneratePayroll
{
    private User $user;

    public function __construct(
        private readonly TimeReport $report,
        private readonly ?User $causer,
    ) {
        $this->user = $this->report->user;
    }

    public static function make(TimeReport $report, ?User $causer): self
    {
        return new self($report, $causer);
    }

    public function execute(): Payroll
    {
        /** @var ?Payroll $previous */
        $previous = $this->user->payrolls()
            ->where('till', '<', $this->report->date)
            ->orderByDesc('till')
            ->first();

        $from = isset($previous) ? $previous->till->addDay() : $this->report->date->startOfMonth();
        $till = $this->report->date;

        $minutes = $this->user->timeReports()
            ->where('date', '>=', $from)
            ->where('date', '<=', $till)
            ->sum('overtime_minutes');
        $timePlan = FindTimePlanForDate::make($this->user, $this->report->date)->execute();
        $hourly_wage = $timePlan->wage ?? 0.00;
        $prev_balance = $previous?->current_balance ?? 0.00;

        $payroll = $this->user->payrolls()->create([
            'time_report_id' => $this->report->id,
            'previous_id' => $previous?->id ?? null,
            'from' => $from,
            'till' => $till,
            'minutes' => $minutes,
            'hourly_wage' => $hourly_wage,
            'payment' => $minutes / 60 * $hourly_wage,
            'extra_payment' => 0.00,
            'prev_balance' => $prev_balance,
            'payout' => 0.00,
            'current_balance' => $prev_balance,
            'meta' => [
                'created_by_id' => $this->causer->id,
            ],
        ]);

        return $payroll;
    }
}
