<?php

namespace App\Listeners;

use App\Notifications\Appointments\AppointmentNotification;
use App\Notifications\Customers\CustomerNotification;
use App\Notifications\Invoices\InvoiceNotification;
use Illuminate\Notifications\Events\NotificationSent;

class LogNotification
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
    public function handle(NotificationSent $event): void
    {
        if ($event->notification instanceof AppointmentNotification) {

        }

        if ($event->notification instanceof CustomerNotification) {

        }

        if ($event->notification instanceof InvoiceNotification) {
            
        }
    }
}
