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
        Schema::table('availability_shifts', function (Blueprint $table) {
            $table->dropColumn('day_of_month');
            $table->date('start_date')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_shifts', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->integer('day_of_month')->nullable()->index();
        });
    }
};
