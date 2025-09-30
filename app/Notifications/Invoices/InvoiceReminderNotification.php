<?php

namespace App\Notifications\Invoices;

use App\Enums\Notifications\NotificationType;
use App\Models\NotificationTemplate;

class InvoiceReminderNotification extends InvoiceNotification
{
    protected function getNotificationTemplate(): ?NotificationTemplate
    {
        return NotificationTemplate::query()
            ->where('type', NotificationType::InvoiceReminder)
            ->first();
    }
}
