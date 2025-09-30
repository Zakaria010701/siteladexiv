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
        Schema::create('availability_shifts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\Availability::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Room::class)->nullable()->constrained()->cascadeOnDelete();

            $table->time('start')->nullable();
            $table->time('end')->nullable();

            $table->integer('weekday')->nullable()->index();
            $table->integer('day_of_month')->nullable()->index();
            $table->string('repeat_step');
            $table->integer('repeat_every');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_shifts');
    }
};
