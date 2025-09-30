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
            $table->foreignIdFor(\App\Models\TimeReportOverview::class, 'previous_id')->nullable()->after('user_id')->constrained('time_report_overviews');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_report_overviews', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\TimeReportOverview::class, 'previous_id');
        });
    }
};
