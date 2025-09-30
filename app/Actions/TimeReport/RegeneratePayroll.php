<?php

namespace App\Actions\TimeReport;

use App\Models\Payroll;

class RegeneratePayroll
{
    public function __construct(
        private Payroll $payroll,
        private readonly ?Payroll $previous,
    ) {}

    public static function make(Payroll $payroll, ?Payroll $previous = null): self
    {
        if (is_null($previous)) {
            $previous = $payroll
                ->where('till', '<=', $payroll->from)
                ->orderByDesc('till')
                ->first();
        }

        return new self($payroll, $previous);
    }

    public function execute(): Payroll
    {
        $this->payroll->previous_id = $this->previous?->id ?? null;
        $this->payroll->from = isset($this->previous) ? $this->previous->till->addDay() : $this->payroll->from;

        $this->payroll->minutes = $this->payroll->user->timeReports()
            ->where('date', '>=', $this->payroll->from)
            ->where('date', '<=', $this->payroll->till)
            ->sum('overtime_minutes');
        $timePlan = FindTimePlanForDate::make($this->payroll->user, $this->payroll->till)->execute();
        $this->payroll->hourly_wage = $timePlan->hourly_wage ?? 0.00;
        $this->payroll->prev_balance = $this->previous?->current_balance ?? 0.00;
        $this->payroll->current_balance = $this->payroll->prev_balance + $this->payroll->payment + $this->payroll->extra_payment - $this->payroll->payout;

        $this->payroll->save();

        return $this->payroll;
    }
}
