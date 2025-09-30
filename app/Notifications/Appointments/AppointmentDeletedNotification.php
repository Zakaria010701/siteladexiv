<?php

namespace App\Notifications\Appointments;

use App\Enums\Notifications\NotificationType;
use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Builder;

class AppointmentDeletedNotification extends AppointmentNotification
{
    protected function getNotificationTemplate(): ?NotificationTemplate
    {
        return NotificationTemplate::query()
            ->where('type', NotificationType::AppointmentDeleted)
            ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $this->appointment->branch_id))
            ->first();
    }
}
