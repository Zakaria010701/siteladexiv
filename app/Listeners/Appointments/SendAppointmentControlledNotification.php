<?php

namespace App\Listeners\Appointments;

use App\Events\Appointments\AppointmentControlledEvent;
use App\Models\Customer;
use App\Notifications\Appointments\AppointmentControlledNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentControlledNotification implements ShouldHandleEventsAfterCommit, ShouldQueue
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
    public function handle(AppointmentControlledEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        if (is_null($event->appointment->customer)) {
            return;
        }

        $event->appointment->customer->notify(new AppointmentControlledNotification($event->appointment));
        $event->appointment->participants->each(
            fn (Customer $participant) => $participant->notify(new AppointmentControlledNotification($event->appointment))
        );
    }
}
