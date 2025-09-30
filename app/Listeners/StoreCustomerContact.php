<?php

namespace App\Listeners;

use App\Models\CustomerContact;
use App\Notifications\Appointments\AppointmentNotification;
use App\Notifications\Customers\CustomerNotification;
use Illuminate\Notifications\Events\NotificationSent;

class StoreCustomerContact
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
        if ($event->notification instanceof CustomerNotification) {
            $customer = $event->notification->getCustomer();

            /** @var CustomerContact $contact */
            $contact = $customer->customerContacts()->create([
                'channel' => $event->channel,
                'title' => $event->notification->getTitle(),
                'message' => $event->notification->getMessage(),
            ]);

            $contact->subject()->associate($event->notification->getSubject());
        }

        if ($event->notification instanceof AppointmentNotification) {
            $customer = $event->notification->getAppointment()->customer;

            /** @var CustomerContact $contact */
            $contact = $customer->customerContacts()->create([
                'channel' => $event->channel,
                'title' => $event->notification->getTitle(),
                'message' => $event->notification->getMessage(),
            ]);

            $contact->subject()->associate($event->notification->getAppointment());
        }
    }
}
