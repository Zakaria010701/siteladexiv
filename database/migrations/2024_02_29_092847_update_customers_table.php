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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('primary_email')->unique();
            $table->string('phone_number')->nullable()->index();
            $table->string('mobile_number')->nullable()->index();

            $table->string('password')->nullable();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('primary_email');
            $table->dropColumn('phone_number');
            $table->dropColumn('mobile_number');

            $table->dropColumn('password');
            $table->dropRememberToken();
        });
    }
};
