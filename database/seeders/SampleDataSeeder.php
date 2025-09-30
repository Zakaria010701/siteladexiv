<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSampleUsers();
        $this->createSampleCustomers();
        $this->createSampleAppointments();
        $this->createSampleServices();
    }

    private function createSampleUsers(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'current_branch_id' => 1,
                'is_provider' => true,
            ],
            [
                'id' => 2,
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@viderma.de',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'current_branch_id' => 1,
                'is_provider' => true,
            ],
            [
                'id' => 3,
                'name' => 'Dr. Michael Weber',
                'email' => 'michael.weber@viderma.de',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'current_branch_id' => 2,
                'is_provider' => true,
            ],
            [
                'id' => 4,
                'name' => 'Lisa Schmidt',
                'email' => 'lisa.schmidt@viderma.de',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'current_branch_id' => 1,
                'is_provider' => false,
            ],
            [
                'id' => 5,
                'name' => 'Thomas Müller',
                'email' => 'thomas.mueller@viderma.de',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'current_branch_id' => 2,
                'is_provider' => false,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['id' => $userData['id']],
                $userData
            );
        }

        // Create additional random users
        User::factory()->count(10)->create([
            'current_branch_id' => 1,
            'is_provider' => false,
        ]);
    }

    private function createSampleCustomers(): void
    {
        $customers = [
            [
                'id' => 1,
                'firstname' => 'Anna',
                'lastname' => 'Kowalski',
                'email' => 'anna.kowalski@example.com',
                'phone' => '+49 69 12345678',
                'birthday' => '1985-03-15',
                'gender' => 'female',
                'branch_id' => 1,
                'prefered_contact_method' => 'email',
            ],
            [
                'id' => 2,
                'firstname' => 'Peter',
                'lastname' => 'Schmidt',
                'email' => 'peter.schmidt@example.com',
                'phone' => '+49 69 87654321',
                'birthday' => '1978-11-22',
                'gender' => 'male',
                'branch_id' => 1,
                'prefered_contact_method' => 'phone',
            ],
            [
                'id' => 3,
                'firstname' => 'Maria',
                'lastname' => 'Rodriguez',
                'email' => 'maria.rodriguez@example.com',
                'phone' => '+49 611 5556677',
                'birthday' => '1990-07-08',
                'gender' => 'female',
                'branch_id' => 2,
                'prefered_contact_method' => 'email',
            ],
            [
                'id' => 4,
                'firstname' => 'Hans',
                'lastname' => 'Fischer',
                'email' => 'hans.fischer@example.com',
                'phone' => '+49 611 7778899',
                'birthday' => '1982-12-03',
                'gender' => 'male',
                'branch_id' => 2,
                'prefered_contact_method' => 'sms',
            ],
            [
                'id' => 5,
                'firstname' => 'Sophie',
                'lastname' => 'Wagner',
                'email' => 'sophie.wagner@example.com',
                'phone' => '+49 69 9998887',
                'birthday' => '1988-05-17',
                'gender' => 'female',
                'branch_id' => 1,
                'prefered_contact_method' => 'email',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::updateOrCreate(
                ['id' => $customerData['id']],
                $customerData
            );
        }

        // Create additional random customers
        Customer::factory()->count(50)->create();
    }

    private function createSampleAppointments(): void
    {
        $tomorrow = now()->addDays(1);
        $dayAfterTomorrow = now()->addDays(2);
        $threeDaysFromNow = now()->addDays(3);
        $fourDaysFromNow = now()->addDays(4);
        $fiveDaysFromNow = now()->addDays(5);

        $appointments = [
            [
                'id' => 1,
                'customer_id' => 1,
                'branch_id' => 1,
                'start_time' => $tomorrow->setHour(10)->setMinute(0),
                'end_time' => $tomorrow->copy()->setHour(11)->setMinute(0),
                'status' => 'confirmed',
                'notes' => 'Laser hair removal consultation',
                'difficulty_score' => 2,
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'branch_id' => 1,
                'start_time' => $dayAfterTomorrow->setHour(14)->setMinute(30),
                'end_time' => $dayAfterTomorrow->copy()->setHour(15)->setMinute(30),
                'status' => 'confirmed',
                'notes' => 'Follow-up treatment',
                'difficulty_score' => 1,
            ],
            [
                'id' => 3,
                'customer_id' => 3,
                'branch_id' => 2,
                'start_time' => $threeDaysFromNow->setHour(9)->setMinute(0),
                'end_time' => $threeDaysFromNow->copy()->setHour(10)->setMinute(0),
                'status' => 'pending',
                'notes' => 'Initial consultation',
                'difficulty_score' => 3,
            ],
            [
                'id' => 4,
                'customer_id' => 4,
                'branch_id' => 2,
                'start_time' => $fourDaysFromNow->setHour(16)->setMinute(0),
                'end_time' => $fourDaysFromNow->copy()->setHour(17)->setMinute(0),
                'status' => 'confirmed',
                'notes' => 'Skin treatment session',
                'difficulty_score' => 2,
            ],
            [
                'id' => 5,
                'customer_id' => 5,
                'branch_id' => 1,
                'start_time' => $fiveDaysFromNow->setHour(11)->setMinute(30),
                'end_time' => $fiveDaysFromNow->copy()->setHour(12)->setMinute(30),
                'status' => 'confirmed',
                'notes' => 'Maintenance treatment',
                'difficulty_score' => 1,
            ],
        ];

        foreach ($appointments as $appointmentData) {
            Appointment::updateOrCreate(
                ['id' => $appointmentData['id']],
                $appointmentData
            );
        }

        // Create additional random appointments
        Appointment::factory()->count(25)->create();
    }

    private function createSampleServices(): void
    {
        $services = [
            [
                'id' => 1,
                'name' => 'Laser Hair Removal - Full Body',
                'description' => 'Complete laser hair removal treatment for full body',
                'duration_minutes' => 120,
                'price' => 299.00,
                'category_id' => 1,
                'branch_id' => 1,
                'next_appointment_in' => 28,
                'customer_needs_to_arrive_prior_to_appointment' => 15,
            ],
            [
                'id' => 2,
                'name' => 'Laser Hair Removal - Face',
                'description' => 'Laser hair removal treatment for face area',
                'duration_minutes' => 30,
                'price' => 89.00,
                'category_id' => 1,
                'branch_id' => 1,
                'next_appointment_in' => 21,
                'customer_needs_to_arrive_prior_to_appointment' => 10,
            ],
            [
                'id' => 3,
                'name' => 'Skin Rejuvenation',
                'description' => 'Advanced skin rejuvenation treatment',
                'duration_minutes' => 60,
                'price' => 149.00,
                'category_id' => 2,
                'branch_id' => 1,
                'next_appointment_in' => 14,
                'customer_needs_to_arrive_prior_to_appointment' => 15,
            ],
            [
                'id' => 4,
                'name' => 'Anti-Aging Treatment',
                'description' => 'Comprehensive anti-aging facial treatment',
                'duration_minutes' => 90,
                'price' => 199.00,
                'category_id' => 2,
                'branch_id' => 2,
                'next_appointment_in' => 30,
                'customer_needs_to_arrive_prior_to_appointment' => 15,
            ],
            [
                'id' => 5,
                'name' => 'Consultation',
                'description' => 'Initial consultation and skin analysis',
                'duration_minutes' => 30,
                'price' => 0.00,
                'category_id' => 3,
                'branch_id' => 1,
                'next_appointment_in' => null,
                'customer_needs_to_arrive_prior_to_appointment' => 10,
            ],
        ];

        foreach ($services as $serviceData) {
            \App\Models\Service::updateOrCreate(
                ['id' => $serviceData['id']],
                $serviceData
            );
        }
    }
}