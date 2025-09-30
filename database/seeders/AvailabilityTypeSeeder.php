<?php

namespace Database\Seeders;

use App\Models\AvailabilityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailabilityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AvailabilityType::upsert([
            [
                'id' => 1,
                'name' => 'Anbieter',
                'color' => '#3788d8',
                'is_hidden' => false,
                'is_all_day' => true,
                'is_background' => true,
                'is_background_inverted' => true,
            ],
            [
                'id' => 2,
                'name' => 'Rezeption',
                'color' => '#f542bc',
                'is_hidden' => false,
                'is_all_day' => true,
                'is_background' => false,
                'is_background_inverted' => false,
            ],
            [
                'id' => 3,
                'name' => 'Admin',
                'color' => '#4CAF50',
                'is_hidden' => true,
                'is_all_day' => false,
                'is_background' => false,
                'is_background_inverted' => false,
            ],
            [
                'id' => 4,
                'name' => 'Resource',
                'color' => '#E36E0C',
                'is_hidden' => false,
                'is_all_day' => true,
                'is_background' => false,
                'is_background_inverted' => false,
            ],
        ], ['id']);
    }
}
