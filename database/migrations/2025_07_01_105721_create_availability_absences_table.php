<?php

use App\Models\Availability;
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
        Schema::create('availability_absences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Availability::class)->constrained()->cascadeOnDelete();

            $table->date('start_date')->index();
            $table->date('end_date')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_absences');
    }
};
