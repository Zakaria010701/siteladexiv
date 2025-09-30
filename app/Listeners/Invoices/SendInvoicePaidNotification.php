<?php

namespace App\Listeners\Invoices;

use App\Events\Invoices\InvoicePaidEvent;
use App\Notifications\Invoices\InvoicePaidNotification;

class SendInvoicePaidNotification
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
    public function handle(InvoicePaidEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        $event->invoice->recipient->notify(new InvoicePaidNotification($event->invoice));
    }
}
