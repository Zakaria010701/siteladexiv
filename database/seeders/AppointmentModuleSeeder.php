<?php

namespace Database\Seeders;

use App\Enums\Appointments\AppointmentModule;
use App\Enums\Appointments\AppointmentType;
use App\Models\AppointmentModuleSetting;
use Illuminate\Database\Seeder;

class AppointmentModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentModuleSetting::upsert([
            [
                'name' => AppointmentModule::Participants->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                    AppointmentType::Debriefing->value,
                    AppointmentType::FollowUp->value,
                    AppointmentType::RoomBlock->value,
                ]),
            ],
            [
                'name' => AppointmentModule::Order->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                    AppointmentType::Debriefing->value,
                    AppointmentType::FollowUp->value,
                ]),
            ],
            [
                'name' => AppointmentModule::Status->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                    AppointmentType::Debriefing->value,
                    AppointmentType::FollowUp->value,
                ]),
            ],
            [
                'name' => AppointmentModule::Extras->value,
                'appointment_types' => json_encode([
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                ]),
            ],
            [
                'name' => AppointmentModule::Done->value,
                'appointment_types' => json_encode([
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                ]),
            ],
            [
                'name' => AppointmentModule::Consultation->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::TreatmentConsultation->value,
                ]),
            ],
            [
                'name' => AppointmentModule::Notes->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                    AppointmentType::Debriefing->value,
                    AppointmentType::FollowUp->value,
                ]),
            ],
            [
                'name' => AppointmentModule::History->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                    AppointmentType::Debriefing->value,
                    AppointmentType::FollowUp->value,
                ]),
            ],
            [
                'name' => AppointmentModule::ServiceDetails->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                    AppointmentType::Debriefing->value,
                    AppointmentType::FollowUp->value,
                ]),
            ],
            [
                'name' => AppointmentModule::Complaint->value,
                'appointment_types' => json_encode([
                    AppointmentType::Consultation->value,
                    AppointmentType::Treatment->value,
                    AppointmentType::TreatmentConsultation->value,
                    AppointmentType::Debriefing->value,
                    AppointmentType::FollowUp->value,
                ]),
            ],
        ], ['name']);
    }
}
