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
        Schema::create('time_plans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\User::class)->constrained();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->integer('monday_hours')->default(0);
            $table->integer('tuesday_hours')->default(0);
            $table->integer('wednesday_hours')->default(0);
            $table->integer('thursday_hours')->default(0);
            $table->integer('friday_hours')->default(0);
            $table->integer('saturday_hours')->default(0);
            $table->integer('sunday_hours')->default(0);

            $table->string('time_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_plans');
    }
};
