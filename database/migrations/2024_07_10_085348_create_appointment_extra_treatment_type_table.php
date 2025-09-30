<?php

use App\Models\AppointmentExtra;
use App\Models\TreatmentType;
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
        Schema::create('appointment_extra_treatment_type', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(AppointmentExtra::class)->constrained();
            $table->foreignIdFor(TreatmentType::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_extra_treatment_type');
    }
};
