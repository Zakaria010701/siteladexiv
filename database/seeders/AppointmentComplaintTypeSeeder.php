<?php

namespace Database\Seeders;

use App\Models\AppointmentComplaintType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentComplaintTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentComplaintType::upsert([
            [
                'id' => 1,
                "name" => "Zahlung",
            ],
            [
                'id' => 2,
                "name" => "Unzufrieden",
            ],
            [
                'id' => 3,
                "name" => "Rückruf zwecks Klärung",
            ],
            [
                'id' => 4,
                "name" => "Schlechte Behandlung",
            ],
            [
                'id' => 5,
                "name" => "Beschwerde über Behandler",
            ],
            [
                'id' => 6,
                "name" => "Allgemeine Beschwerde",
            ],
            [
                'id' => 7,
                "name" => "Preis zu hoch",
            ],
            [
                'id' => 8,
                "name" => "Verbrennung",
            ],
        ], ['id']);
    }
}
