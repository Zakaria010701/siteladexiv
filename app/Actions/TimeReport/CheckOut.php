<?php

namespace App\Actions\TimeReport;

use App\Enums\TimeRecords\TimeCheckStatus;
use App\Models\TimeReport;
use App\Models\User;

class CheckOut
{
    public function __construct(
        private readonly User $user,
        private TimeReport $report,
        private readonly string $note,
        private readonly ?array $meta,
    ) {}

    public static function make(User $user, TimeReport $report, string $note = '', ?array $meta = null): self
    {
        return new self($user, $report, $note, $meta);
    }

    public function execute(): TimeReport
    {
        $timeOut = now()->startOfMinute();
        $this->report->time_out = $timeOut;
        $this->report->real_time_out = $timeOut;
        $this->report->time_out_status = $this->getTimeOutStatus();

        if (! empty($this->note)) {
            $this->report->note .= ' '.__('Check Out: :note', ['note' => $this->note]);
        }

        // Append Meta
        if (! empty($this->meta)) {
            if (! empty($this->report->meta)) {
                array_push($this->report->meta, $this->meta);
            } else {
                $this->report->meta = $this->meta;
            }
        }

        $this->report = RecalculateTimeReport::make($this->report)->skipSave()->excecute();

        $this->report->save();

        return $this->report;
    }

    private function getTimeOutStatus(): TimeCheckStatus
    {
        if (is_null($this->report->work_time_end)) {
            return TimeCheckStatus::Ok;
        }

        // Check if the TimeOut was early
        if (
            $this->report->time_out
                ->subMinutes(timeSettings()->early_check_out_minutes)
                ->lt($this->report->work_time_end)
        ) {
            return TimeCheckStatus::Early;
        }

        // Check if the TimeOut was late
        if (
            $this->report->time_out
                ->addMinutes(timeSettings()->late_check_out_minutes)
                ->gt($this->report->work_time_end)
        ) {
            return TimeCheckStatus::Late;
        }

        return TimeCheckStatus::Ok;
    }
}
