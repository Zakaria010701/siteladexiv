<?php

namespace App\Listeners\Invoices;

use App\Events\Invoices\InvoiceDueEvent;
use App\Notifications\Invoices\InvoiceDueNotification;

class SendInvoiceDueNotification
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
    public function handle(InvoiceDueEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        $event->invoice->recipient->notify(new InvoiceDueNotification($event->invoice));
    }
}
