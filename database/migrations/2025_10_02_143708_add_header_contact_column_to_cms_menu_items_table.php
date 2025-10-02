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
        Schema::table('cms_menu_items', function (Blueprint $table) {
            $table->boolean('header_contact')->nullable()->default(false)->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_menu_items', function (Blueprint $table) {
            $table->dropColumn('header_contact');
        });
    }
};
