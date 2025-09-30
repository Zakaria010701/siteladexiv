<?php

namespace App\Observers;

use App\Hooks\Appointments\AfterCreateAppointment;
use App\Hooks\Appointments\AfterUpdateAppointment;
use App\Integration\GoogleCalendar\Event;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\Customer;
use App\Notifications\Appointments\AppointmentDeletedNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class AppointmentObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        AfterCreateAppointment::make($appointment)->execute();
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void {}

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        $appointment->appointmentItems->each(function (AppointmentItem $item) {
            $item->usedServiceCredits()
                ->orderBy('used_at')
                ->update([
                    'used_at' => null,
                    'usage_type' => null,
                    'usage_id' => null,
                ]);
            $item->orderedServiceCredits()->unused()->delete();
        });

        if (! is_null($appointment->customer)) {
            $appointment->customer->notify(new AppointmentDeletedNotification($appointment));
            $appointment->participants->each(
                fn (Customer $participant) => $participant->notify(new AppointmentDeletedNotification($appointment))
            );
        }

        if(! is_null($appointment->google_event_id)) {
            $event = Event::find($appointment->google_event_id);
            if(isset($event)) {
                $event->delete();
            }
        }
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        AfterUpdateAppointment::make($appointment)->execute();
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
