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
            $table->integer('yearly_vacation_days')->default(0);
            $table->integer('start_vacation_days')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_plans', function (Blueprint $table) {
            $table->dropColumn('yearly_vacation_days');
            $table->dropColumn('start_vacation_days');
        });
    }
};
