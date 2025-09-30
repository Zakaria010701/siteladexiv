<?php

namespace Database\Seeders;

use App\Enums\Notifications\NotificationType;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NotificationTemplate::upsert([
            [
                'id' => 1,
                'type' => NotificationType::AppointmentApproved->value,
                'subject' => 'Ihr Termin wurde bestätigt',
                'is_enabled' => false,
                'enable_mail' => false,
                'enable_sms' => false,
            ],
            [
                'id' => 2,
                'type' => NotificationType::AppointmentPending->value,
                'subject' => 'Ihr Termin',
                'is_enabled' => false,
                'enable_mail' => false,
                'enable_sms' => false,
            ],
            [
                'id' => 3,
                'type' => NotificationType::AppointmentCanceled->value,
                'subject' => 'Ihr Termin wurde abgesagt',
                'is_enabled' => false,
                'enable_mail' => false,
                'enable_sms' => false,
            ], [
                'id' => 4,
                'type' => NotificationType::AppointmentReminder->value,
                'subject' => 'Erinnerung an Ihren Termin',
                'is_enabled' => false,
                'enable_mail' => false,
                'enable_sms' => false,
            ], [
                'id' => 5,
                'type' => NotificationType::AppointmentFollowUp->value,
                'subject' => 'Ihr Termin',
                'is_enabled' => false,
                'enable_mail' => false,
                'enable_sms' => false,
            ], [
                'id' => 6,
                'type' => NotificationType::AppointmentMoved->value,
                'subject' => 'Ihr Termin wurde verschoben',
                'is_enabled' => false,
                'enable_mail' => false,
                'enable_sms' => false,
            ], [
                'id' => 7,
                'type' => NotificationType::AppointmentDeleted->value,
                'subject' => 'Ihr Termin wurde gelöscht',
                'is_enabled' => false,
                'enable_mail' => false,
                'enable_sms' => false,
            ],
        ], ['id']);
    }
}
