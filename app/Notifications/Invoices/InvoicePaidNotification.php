<?php

namespace App\Notifications\Invoices;

use App\Enums\Notifications\NotificationType;
use App\Models\NotificationTemplate;

class InvoicePaidNotification extends InvoiceNotification
{
    protected function getNotificationTemplate(): ?NotificationTemplate
    {
        return NotificationTemplate::query()
            ->where('type', NotificationType::InvoicePaid)
            ->first();
    }
}
