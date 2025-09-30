<?php

namespace Database\Factories;

use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeThisMonth();

        return [
            'start' => $date,
            'end' => Carbon::parse($date)->addMinutes(15 * fake()->randomDigitNotZero()),
            'status' => AppointmentStatus::Pending->value,
            'type' => fake()->randomElement(AppointmentType::cases())->value,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Approved,
            'approved_at' => fake()->dateTimeThisMonth(),
        ]);

    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Done,
            'done_at' => fake()->dateTimeThisMonth(),
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Canceled,
            'canceled_at' => fake()->dateTimeThisMonth(),
        ]);
    }
}
