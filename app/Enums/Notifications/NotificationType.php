<?php

namespace App\Enums\Notifications;

use Filament\Support\Contracts\HasLabel;

enum NotificationType: string implements HasLabel
{
    // Appointment Notifications
    case AppointmentApproved = 'appointment_approved';
    case AppointmentPending = 'appointment_pending';
    case AppointmentCanceled = 'appointment_canceled';
    case AppointmentReminder = 'appointment_reminder';
    case AppointmentFollowUp = 'appointment_follow_up';
    case AppointmentMoved = 'appointment_moved';
    case AppointmentCheckIn = 'appointment_check_in';
    case AppointmentCheckOut = 'appointment_check_out';
    case AppointmentControlled = 'appointment_controlled';
    case AppointmentConfirmed = 'appointment_confirmed';
    case AppointmentDeleted = 'appointment_deleted';

    // Invoice Notifications
    case InvoiceInfo = 'invoice_info';
    case InvoiceCanceled = 'invoice_canceled';
    case InvoiceDebtCollection = 'invoice_debt_collection';
    case InvoiceDue = 'invoice_due';
    case InvoicePaid = 'invoice_paid';
    case InvoiceReminder = 'invoice_reminder';

    public function getLabel(): ?string
    {
        return __("notification.type.$this->value");
    }
}
