<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('street')->nullable();
            $table->string('postcode')->nullable();
            $table->string('location')->nullable();
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->date('birthday')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
            $table->dropColumn('street');
            $table->dropColumn('postcode');
            $table->dropColumn('location');
            $table->dropColumn('telephone');
            $table->dropColumn('mobile');
            $table->dropColumn('birthday');
        });
    }
};
