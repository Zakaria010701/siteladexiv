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
        Schema::create('availability_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name')->index();

            $table->string('color');
            $table->boolean('is_hidden');
            $table->boolean('is_all_day');
            $table->boolean('is_background');
            $table->boolean('is_background_inverted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_types');
    }
};
