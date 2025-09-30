<?php

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
        Schema::create('appointment_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\Appointment::class)->constrained()->cascadeOnDelete();

            $table->string('hair_type')->nullable();
            $table->string('pigment_type')->nullable();
            $table->integer('skin_type')->nullable();
            $table->integer('satisfaction')->nullable();
            $table->double('energy')->nullable();
            $table->integer('li_count')->nullable();
            $table->integer('spot_size')->nullable();
            $table->integer('wave_length')->nullable();
            $table->integer('milliseconds')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_details');
    }
};
