<?php

namespace Database\Seeders;

use App\Models\ModuleSetting;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModuleSetting::upsert([
            [
                'name' => 'work_time',
                'active' => true,
            ],
            [
                'name' => 'appointment_extra',
                'active' => true,
            ],
        ], ['name']);
    }
}
