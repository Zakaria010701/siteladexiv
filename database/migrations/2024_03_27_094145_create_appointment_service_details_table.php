<?php

use App\Models\Appointment;
use App\Models\Service;
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
        Schema::create('appointment_service_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Appointment::class, 'appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Service::class, 'service_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->decimal('spot_size')->nullable();
            $table->decimal('energy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_service_details');
    }
};
