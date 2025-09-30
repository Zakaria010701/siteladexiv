<?php

namespace Database\Seeders;

use App\Enums\Appointments\AppointmentExtraType;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Appointments\Extras\HairType;
use App\Models\AppointmentExtra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentExtra::upsert([
            [
                'id' => 1,
                'type' => AppointmentExtraType::HairType->value,
                'default' => HairType::Normal->value,
                'is_required' => true,
                'take_from_last_appointment' => true,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 2,
                'type' => AppointmentExtraType::PigmentType->value,
                'default' => null,
                'is_required' => true,
                'take_from_last_appointment' => true,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 3,
                'type' => AppointmentExtraType::Satisfaction->value,
                'default' => null,
                'is_required' => true,
                'take_from_last_appointment' => false,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 4,
                'type' => AppointmentExtraType::SkinType->value,
                'default' => null,
                'is_required' => true,
                'take_from_last_appointment' => true,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 5,
                'type' => AppointmentExtraType::Energy->value,
                'default' => null,
                'is_required' => true,
                'take_from_last_appointment' => false,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 6,
                'type' => AppointmentExtraType::LiCount->value,
                'default' => null,
                'is_required' => true,
                'take_from_last_appointment' => false,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 8,
                'type' => AppointmentExtraType::SpotSize->value,
                'default' => null,
                'is_required' => true,
                'take_from_last_appointment' => false,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 9,
                'type' => AppointmentExtraType::Color->value,
                'default' => null,
                'is_required' => false,
                'take_from_last_appointment' => true,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 10,
                'type' => AppointmentExtraType::WaveLength->value,
                'default' => null,
                'is_required' => false,
                'take_from_last_appointment' => true,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
            [
                'id' => 11,
                'type' => AppointmentExtraType::Milliseconds->value,
                'default' => null,
                'is_required' => true,
                'take_from_last_appointment' => false,
                'appointment_types' => json_encode([AppointmentType::Treatment, AppointmentType::Consultation, AppointmentType::TreatmentConsultation]),
            ],
        ], ['id']);

        DB::table('appointment_extra_category')->upsert([
            // Hair Type
            ['id' => 1, 'appointment_extra_id' => 1, 'category_id' => 1],
            ['id' => 2, 'appointment_extra_id' => 1, 'category_id' => 3],
            ['id' => 3, 'appointment_extra_id' => 1, 'category_id' => 9],
            ['id' => 4, 'appointment_extra_id' => 1, 'category_id' => 17],
            // Pigment Type
            ['id' => 5, 'appointment_extra_id' => 2, 'category_id' => 3],
            ['id' => 6, 'appointment_extra_id' => 2, 'category_id' => 6],
            ['id' => 7, 'appointment_extra_id' => 2, 'category_id' => 9],
            ['id' => 8, 'appointment_extra_id' => 2, 'category_id' => 1],
            // Satisfaction
            ['id' => 10, 'appointment_extra_id' => 3, 'category_id' => 9],
            ['id' => 11, 'appointment_extra_id' => 3, 'category_id' => 8],
            ['id' => 12, 'appointment_extra_id' => 3, 'category_id' => 1],
            ['id' => 13, 'appointment_extra_id' => 3, 'category_id' => 3],
            ['id' => 14, 'appointment_extra_id' => 3, 'category_id' => 4],
            ['id' => 15, 'appointment_extra_id' => 3, 'category_id' => 7],
            ['id' => 16, 'appointment_extra_id' => 3, 'category_id' => 5],
            ['id' => 17, 'appointment_extra_id' => 3, 'category_id' => 6],
            // Skin Type
            ['id' => 18, 'appointment_extra_id' => 4, 'category_id' => 7],
            ['id' => 19, 'appointment_extra_id' => 4, 'category_id' => 4],
            ['id' => 20, 'appointment_extra_id' => 4, 'category_id' => 6],
            ['id' => 21, 'appointment_extra_id' => 4, 'category_id' => 1],
            ['id' => 22, 'appointment_extra_id' => 4, 'category_id' => 3],
            ['id' => 23, 'appointment_extra_id' => 4, 'category_id' => 9],
            // Energy
            ['id' => 24, 'appointment_extra_id' => 5, 'category_id' => 6],
            ['id' => 25, 'appointment_extra_id' => 5, 'category_id' => 4],
            ['id' => 26, 'appointment_extra_id' => 5, 'category_id' => 1],
            ['id' => 27, 'appointment_extra_id' => 5, 'category_id' => 17],
            ['id' => 28, 'appointment_extra_id' => 5, 'category_id' => 3],
            ['id' => 29, 'appointment_extra_id' => 5, 'category_id' => 9],
            ['id' => 30, 'appointment_extra_id' => 5, 'category_id' => 22],
            // LiCount
            ['id' => 31, 'appointment_extra_id' => 6, 'category_id' => 17],
            ['id' => 32, 'appointment_extra_id' => 6, 'category_id' => 6],
            ['id' => 33, 'appointment_extra_id' => 6, 'category_id' => 9],
            ['id' => 34, 'appointment_extra_id' => 6, 'category_id' => 3],
            ['id' => 35, 'appointment_extra_id' => 6, 'category_id' => 1],
            ['id' => 36, 'appointment_extra_id' => 6, 'category_id' => 4],
            // SpotSize
            ['id' => 37, 'appointment_extra_id' => 8, 'category_id' => 6],
            ['id' => 38, 'appointment_extra_id' => 8, 'category_id' => 4],
            ['id' => 39, 'appointment_extra_id' => 8, 'category_id' => 17],
            ['id' => 40, 'appointment_extra_id' => 8, 'category_id' => 9],
            ['id' => 41, 'appointment_extra_id' => 8, 'category_id' => 3],
            ['id' => 42, 'appointment_extra_id' => 8, 'category_id' => 1],
            // Color
            ['id' => 43, 'appointment_extra_id' => 9, 'category_id' => 6],
            ['id' => 44, 'appointment_extra_id' => 9, 'category_id' => 4],
            // WaveLength
            ['id' => 45, 'appointment_extra_id' => 10, 'category_id' => 4],
            // Milliseconds
            ['id' => 46, 'appointment_extra_id' => 11, 'category_id' => 3],
            ['id' => 47, 'appointment_extra_id' => 11, 'category_id' => 17],
            ['id' => 48, 'appointment_extra_id' => 11, 'category_id' => 1],
        ], ['id']);
    }
}
