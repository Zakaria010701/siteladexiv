<?php

namespace App\Listeners\Invoices;

use App\Events\Invoices\InvoiceReminderEvent;
use App\Notifications\Invoices\InvoiceReminderNotification;

class SendInvoiceReminderNotification
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
    public function handle(InvoiceReminderEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        $event->invoice->recipient->notify(new InvoiceReminderNotification($event->invoice));
    }
}
