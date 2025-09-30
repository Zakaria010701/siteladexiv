<?php

namespace Database\Seeders;

use App\Enums\TimeRecords\TimeConstraint;
use App\Models\UserWorkType;
use Illuminate\Database\Seeder;

class UserWorkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserWorkType::upsert([
            [
                'id' => 1,
                'name' => 'Vollzeit',
                'time_constraint' => TimeConstraint::Target->value,
            ],
            [
                'id' => 2,
                'name' => 'Aushilfe',
                'time_constraint' => TimeConstraint::Worktime->value,
            ],
        ], ['id']);
    }
}
