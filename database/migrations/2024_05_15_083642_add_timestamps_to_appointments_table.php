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
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('check_in_at')->nullable()->index();
            $table->timestamp('check_out_at')->nullable()->index();
            $table->timestamp('controlled_at')->nullable()->index();
            $table->timestamp('confirmed_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('check_in_at');
            $table->dropColumn('check_out_at');
            $table->dropColumn('controlled_at');
            $table->dropColumn('confirmed_at');
        });
    }
};
