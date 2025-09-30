<?php

namespace App\Listeners\Appointments;

use App\Enums\Appointments\CancelReason;
use App\Events\Appointments\AppointmentCanceledEvent;

class IncreaseDifficultyScoreAfterCancel
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
    public function handle(AppointmentCanceledEvent $event): void
    {
        $increase = match ($event->reason) {
            CancelReason::CustomerNotAppeared => 3,
            CancelReason::SameDayCancellation => 2,
            default => 1,
        };

        $event->appointment->difficulty_score += $increase;
        $event->appointment->saveQuietly();
    }
}
