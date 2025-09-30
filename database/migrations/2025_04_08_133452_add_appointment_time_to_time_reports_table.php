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
        Schema::table('time_reports', function (Blueprint $table) {
            $table->dateTime('appointment_start')->nullable()->after('work_time_minutes');
            $table->dateTime('appointment_end')->nullable()->after('appointment_start');
            $table->integer('appointment_minutes')->nullable()->after('appointment_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_reports', function (Blueprint $table) {
            $table->dropColumn([
                'appointment_start',
                'appointment_end',
                'appointment_minutes'
            ]);
        });
    }
};
