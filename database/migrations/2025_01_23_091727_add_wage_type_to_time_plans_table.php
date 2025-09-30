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
        Schema::table('time_plans', function (Blueprint $table) {
            $table->dropColumn('hourly_wage');
            $table->decimal('wage')->default(0.0);
            $table->string('wage_type')->default('monthly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_plans', function (Blueprint $table) {
            $table->decimal('hourly_wage')->default(0.0);
            $table->dropColumn('wage');
            $table->dropColumn('wage_type');
        });
    }
};
