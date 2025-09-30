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
        Schema::table('appointment_service_details', function (Blueprint $table) {
            $table->dropColumn('spot_size');
            $table->dropColumn('energy');
            $table->dropColumn('li_count');
            $table->double('energy')->nullable();
            $table->integer('li_count')->nullable();
            $table->integer('spot_size')->nullable();
            $table->integer('wave_length')->nullable();
            $table->integer('milliseconds')->nullable();
            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_service_details', function (Blueprint $table) {
            $table->dropColumn('meta');
            $table->dropColumn('milliseconds');
            $table->dropColumn('wave_length');
            $table->dropColumn('spot_size');
            $table->dropColumn('li_count');
            $table->dropColumn('energy');
            $table->decimal('spot_size')->nullable();
            $table->decimal('energy')->nullable();
            $table->decimal('li_count')->nullable();
        });
    }
};
