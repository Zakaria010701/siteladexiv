<?php

namespace App\Listeners\Appointments;

use App\Events\Appointments\AppointmentMovedEvent;

class IncreaseDifficultyScoreAfterMove
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
    public function handle(AppointmentMovedEvent $event): void
    {

        /*if($event->reason !== AppointmentMoveReason::CustomerRequest) {
            return;
        }*/

        $event->appointment->difficulty_score += 1;
        $event->appointment->saveQuietly();
    }
}
