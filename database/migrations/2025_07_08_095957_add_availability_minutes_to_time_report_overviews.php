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
        Schema::table('time_report_overviews', function (Blueprint $table) {
            $table->dropColumn('work_time_minutes');
            $table->integer('availability_minutes')->default(0)->after('target_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_report_overviews', function (Blueprint $table) {
            $table->dropColumn('availability_minutes');
            $table->integer('work_time_minutes')->default(0)->after('target_minutes');
        });
    }
};
