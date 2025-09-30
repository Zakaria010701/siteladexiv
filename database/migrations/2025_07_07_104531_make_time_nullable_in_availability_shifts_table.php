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
            $table->time('start')->nullable()->change();
            $table->time('end')->nullable()->change();
        });

        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->time('start')->nullable()->change();
            $table->time('end')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_shifts', function (Blueprint $table) {
            $table->time('start')->nullable(false)->change();
            $table->time('end')->nullable(false)->change();
        });
        
        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->time('start')->nullable(false)->change();
            $table->time('end')->nullable(false)->change();
        });
    }
};
