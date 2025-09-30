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
        Schema::create('energy_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(TreatmentType::class)->constrained();
            $table->string('hair_type')->nullable()->index();
            $table->string('pigment_type')->nullable()->index();
            $table->string('skin_type')->nullable()->index();
            $table->integer('spot_size')->nullable()->index();
            $table->integer('energy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_settings');
    }
};
