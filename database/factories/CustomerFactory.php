<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\Customer;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'gender' => fake()->randomElement(Gender::cases()),
            'firstname' => fake()->firstName,
            'lastname' => fake()->lastName,
            'email' => fake()->email,
            'phone_number' => fake()->phoneNumber,
            'password' => Hash::make('password'),
        ];
    }
}
