<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::upsert([
            [
                'id' => 1,
                'name' => 'Frankfurt',
                'short_code' => 'F',
                'calendar_start_time' => '06:00:00',
                'calendar_end_time' => '21:30:00',
                'frontend_start_time' => '06:00:00',
                'frontend_end_time' => '21:30:00',
                'open_days' => '[1,2,3,4,5,6]',
            ],
            [
                'id' => 2,
                'name' => 'Wiesbaden',
                'short_code' => 'W',
                'calendar_start_time' => '07:00:00',
                'calendar_end_time' => '21:30:00',
                'frontend_start_time' => '07:00:00',
                'frontend_end_time' => '21:30:00',
                'open_days' => '[1,2,3,4,5,6]',
            ],
            [
                'id' => 3,
                'name' => 'Viderma',
                'short_code' => 'V',
                'calendar_start_time' => '08:00:00',
                'calendar_end_time' => '21:00:00',
                'frontend_start_time' => '08:00:00',
                'frontend_end_time' => '21:00:00',
                'open_days' => '[1,2,3,4,5,6]',
            ],
        ], ['id']);
    }
}
