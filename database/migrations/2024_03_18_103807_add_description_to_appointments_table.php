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
            $table->foreignIdFor(\App\Models\Customer::class)->nullable()->change();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->change();
            $table->foreignIdFor(\App\Models\Category::class)->nullable()->change();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Customer::class)->nullable(false)->change();
            $table->foreignIdFor(\App\Models\User::class)->nullable(false)->change();
            $table->foreignIdFor(\App\Models\Category::class)->nullable(false)->change();
            $table->dropColumn('description');
        });
    }
};
