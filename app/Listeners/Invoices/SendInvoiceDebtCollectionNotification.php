<?php

namespace App\Listeners\Invoices;

use App\Events\Invoices\InvoiceDebtCollectionEvent;
use App\Notifications\Invoices\InvoiceDebtCollectionNotification;

class SendInvoiceDebtCollectionNotification
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
    public function handle(InvoiceDebtCollectionEvent $event): void
    {
        if (! $event->sendNotification) {
            return;
        }

        $event->invoice->recipient->notify(new InvoiceDebtCollectionNotification($event->invoice));
    }
}
