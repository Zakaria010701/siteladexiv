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
        Schema::create('availability_user_options', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\Availability::class)->constrained()->cascadeOnDelete();

            $table->string('user_availability_type');

            $table->integer('yearly_vacation_days')->default(0);
            $table->integer('start_vacation_days')->default(0);

            $table->decimal('wage')->nullable();
            $table->string('wage_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_user_options');
    }
};
