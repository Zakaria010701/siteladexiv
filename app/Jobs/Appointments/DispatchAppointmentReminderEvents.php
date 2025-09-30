<?php

namespace App\Jobs\Appointments;

use App\Events\Appointments\AppointmentReminderEvent;
use App\Models\Appointment;
use App\Models\AppointmentReminderSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class DispatchAppointmentReminderEvents implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AppointmentReminderSetting::pluck('days_before')
            ->unique()
            ->each(fn (int $days) => $this->dispatchReminderBefore($days));
    }

    protected function dispatchReminderBefore($days)
    {
        $date = today()->addDays($days)->toImmutable();
        Appointment::where('start', '>=', $date->startOfDay())
            ->where('start', '<=', $date->endOfDay())
            ->chunk(100, function (Collection $appointments) use ($days) {
                $appointments->each(fn (Appointment $appointment) => AppointmentReminderEvent::dispatch($appointment, $days));
            });
    }
}
