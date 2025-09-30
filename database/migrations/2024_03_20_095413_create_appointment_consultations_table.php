<?php

use App\Models\Appointment;
use App\Models\Customer;
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
        Schema::create('appointment_consultations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Appointment::class, 'appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Customer::class, 'customer_id')->constrained()->cascadeOnDelete();

            $table->string('status')->index();
            $table->boolean('informed_about_risks');
            $table->boolean('has_special_risks');
            $table->string('special_risks')->nullable();
            $table->boolean('takes_medicine');
            $table->string('medicine')->nullable();
            $table->boolean('individual_responsibility_signed');
            $table->boolean('informed_about_consultation_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_consultations');
    }
};
