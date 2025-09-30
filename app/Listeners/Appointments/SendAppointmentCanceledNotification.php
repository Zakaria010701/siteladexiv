<?php

namespace App\Listeners\Appointments;

use App\Events\Appointments\AppointmentCanceledEvent;
use App\Models\Customer;
use App\Notifications\Appointments\AppointmentCanceledNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentCanceledNotification implements ShouldHandleEventsAfterCommit, ShouldQueue
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
    public function handle(AppointmentCanceledEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        if (is_null($event->appointment->customer)) {
            return;
        }

        $event->appointment->customer->notify(new AppointmentCanceledNotification($event->appointment));
        $event->appointment->participants->each(
            fn (Customer $participant) => $participant->notify(new AppointmentCanceledNotification($event->appointment))
        );
    }
}
