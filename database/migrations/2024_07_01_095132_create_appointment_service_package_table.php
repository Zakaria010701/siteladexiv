<?php

use App\Models\Appointment;
use App\Models\ServicePackage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_service_package', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Appointment::class, 'appointment_id')->constrained();
            $table->foreignIdFor(ServicePackage::class, 'service_package_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_service_package');
    }
};
