<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ModuleSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ServiceSeeder::class);
        //$this->call(ServicePackageSeeder::class);
        $this->call(AppointmentModuleSeeder::class);
        $this->call(UserWorkTypeSeeder::class);
        $this->call(TreatmentTypeSeeder::class);
        $this->call(AppointmentExtraSeeder::class);
        $this->call(NotificationTemplateSeeder::class);
        $this->call(AppointmentComplaintTypeSeeder::class);
        $this->call(AvailabilityTypeSeeder::class);
        $this->call(SystemResourceSeeder::class);
        $this->call(SampleDataSeeder::class);
    }
}
