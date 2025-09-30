<?php

namespace App\Listeners\Appointments;

use App\Events\Appointments\AppointmentReminderEvent;
use App\Models\Customer;
use App\Notifications\Appointments\AppointmentReminderNotification;

class SendAppointmentReminderNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppointmentReminderEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        if (is_null($event->appointment->customer)) {
            return;
        }

        $event->appointment->customer->notify(new AppointmentReminderNotification($event->appointment));
        $event->appointment->participants->each(
            fn (Customer $participant) => $participant->notify(new AppointmentReminderNotification($event->appointment))
        );
    }
}
