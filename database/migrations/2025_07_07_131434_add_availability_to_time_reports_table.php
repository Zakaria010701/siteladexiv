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
            $table->dropColumn('work_time_start');
            $table->dropColumn('work_time_end');
            $table->dropColumn('work_time_minutes');

            $table->dateTime('availability_start')->nullable()->after('target_minutes')->index();
            $table->dateTime('availability_end')->nullable()->after('availability_start')->index();
            $table->integer('availability_minutes')->default(0)->after('availability_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_reports', function (Blueprint $table) {
            $table->dropColumn('availability_start');
            $table->dropColumn('availability_end');
            $table->dropColumn('availability_minutes');

            $table->dateTime('work_time_start')->nullable()->after('target_minutes')->index();
            $table->dateTime('work_time_end')->nullable()->after('work_time_start')->index();
            $table->integer('work_time_minutes')->default(0)->after('work_time_end');
        });
    }
};
