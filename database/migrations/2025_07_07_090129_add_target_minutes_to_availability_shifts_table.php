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
            $table->integer('target_minutes')->default(0)->after('end');
        });

        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->integer('target_minutes')->default(0)->after('end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_shifts', function (Blueprint $table) {
            $table->dropColumn('target_minutes');
        });

        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->dropColumn('target_minutes');
        });
    }
};
