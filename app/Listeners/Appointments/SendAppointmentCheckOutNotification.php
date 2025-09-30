<?php

namespace App\Listeners\Appointments;

use App\Events\Appointments\AppointmentCheckedOutEvent;
use App\Models\Customer;
use App\Notifications\Appointments\AppointmentCheckOutNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentCheckOutNotification implements ShouldHandleEventsAfterCommit, ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(AppointmentCheckedOutEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        if (is_null($event->appointment->customer)) {
            return;
        }

        $event->appointment->customer->notify(new AppointmentCheckOutNotification($event->appointment));
        $event->appointment->participants->each(
            fn (Customer $participant) => $participant->notify(new AppointmentCheckOutNotification($event->appointment))
        );
    }
}
