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
        Schema::table('appointment_extras', function (Blueprint $table) {
            $table->boolean('split_per_service')->default(false)->after('take_from_last_appointment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_extras', function (Blueprint $table) {
            $table->dropColumn('split_per_service');
        });
    }
};
