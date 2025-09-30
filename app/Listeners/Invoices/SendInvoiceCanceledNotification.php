<?php

namespace App\Listeners\Invoices;

use App\Events\Invoices\InvoiceCanceledEvent;
use App\Notifications\Invoices\InvoiceCanceledNotification;

class SendInvoiceCanceledNotification
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
    public function handle(InvoiceCanceledEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        $event->invoice->recipient->notify(new InvoiceCanceledNotification($event->invoice));
    }
}
