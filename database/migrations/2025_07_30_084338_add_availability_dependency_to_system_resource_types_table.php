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
        Schema::table('system_resource_types', function (Blueprint $table) {
            $table->boolean('depends_on_availability')->after('depends_on_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_resource_types', function (Blueprint $table) {
            $table->dropColumn('depends_on_availability');
        });
    }
};
