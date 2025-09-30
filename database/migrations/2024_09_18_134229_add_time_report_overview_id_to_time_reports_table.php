<?php

use App\Models\TimeReportOverview;
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
            $table->foreignIdFor(TimeReportOverview::class, 'time_report_overview_id')->after('user_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_reports', function (Blueprint $table) {
            $table->dropForeignIdFor(TimeReportOverview::class, 'time_report_overview_id');
        });
    }
};
