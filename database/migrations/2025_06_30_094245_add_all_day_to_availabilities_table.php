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
        Schema::table('availabilities', function (Blueprint $table) {
            $table->boolean('is_all_day')->default(true)->after('color');
            $table->boolean('is_background')->default(false)->after('is_all_day');
            $table->boolean('is_background_inverted')->default(false)->after('is_background');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn(['is_all_day', 'is_background', 'is_background_inverted']);
        });
    }
};
