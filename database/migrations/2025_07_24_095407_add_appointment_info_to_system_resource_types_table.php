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
            $table->boolean('show_in_appointment')->default(false)->after('name');
            $table->boolean('is_required')->default(false)->after('show_in_appointment');
            $table->boolean('allow_multiple')->default(false)->after('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_resource_types', function (Blueprint $table) {
            $table->dropColumn('show_in_appointment');
            $table->dropColumn('is_required');
            $table->dropColumn('allow_multiple');
        });
    }
};
