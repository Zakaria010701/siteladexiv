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
            $table->dropColumn('end');
        });

        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->dropColumn('end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_shifts', function (Blueprint $table) {
            $table->time('end')->nullable()->after('start');
        });

        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->time('end')->nullable()->after('start');
        });
    }
};
