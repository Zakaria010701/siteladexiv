<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appointment::factory()
            ->count(100)
            ->state(new Sequence(
                function (Sequence $sequence) {
                    $branch = Branch::all()->random();

                    return [
                        'branch_id' => $branch->id,
                        'room_id' => $branch->rooms->random()->id,
                        'customer_id' => Customer::factory()->create(),
                        'category_id' => Category::all()->random(),
                        'user_id' => User::all()->random(),
                    ];
                }
            ))
            ->create();
    }
}
