<?php

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
        Schema::create('treatment_type_spot_size_options', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(TreatmentType::class)->constrained();
            $table->integer('spot_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_type_spot_size_options');
    }
};
