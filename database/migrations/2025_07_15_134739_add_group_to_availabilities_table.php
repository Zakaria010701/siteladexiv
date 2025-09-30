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
            $table->string('group')->default('availability')->after('is_background_inverted')->index();
        });

        Schema::table('availability_types', function (Blueprint $table) {
            $table->string('group')->default('availability')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn('group');
        });

        Schema::table('availability_types', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
