<?php

namespace App\Actions\TimeReport;

use App\Enums\TimeRecords\TimeCheckStatus;
use App\Models\TimeReport;
use App\Models\User;

class CheckIn
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
        $timeIn = now()->startOfMinute();
        $this->report->time_in = $timeIn->format('H:i:s');
        $this->report->real_time_in = $timeIn->format('H:i:s');
        $this->report->time_in_status = $this->getTimeInStatus();

        if (! empty($this->note)) {
            $this->report->note .= __('Check In: :note', ['note' => $this->note]);
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

    private function getTimeInStatus(): TimeCheckStatus
    {
        if (is_null($this->report->work_time_start)) {
            return TimeCheckStatus::Ok;
        }

        // Check if the TimeIn was early
        if (
            $this->report->time_in
                ->subMinutes(timeSettings()->early_check_in_minutes)
                ->lt($this->report->work_time_start)
        ) {
            return TimeCheckStatus::Early;
        }

        // Check if the TimeIn was late
        if (
            $this->report->time_in
                ->addMinutes(timeSettings()->late_check_in_minutes)
                ->gt($this->report->work_time_start)
        ) {
            return TimeCheckStatus::Late;
        }

        return TimeCheckStatus::Ok;
    }
}
