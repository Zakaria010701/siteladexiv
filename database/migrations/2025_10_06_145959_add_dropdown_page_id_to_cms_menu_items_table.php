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
            $table->foreignId('dropdown_page_id')->nullable()->after('url')->constrained('cms_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_menu_items', function (Blueprint $table) {
            $table->dropForeign(['dropdown_page_id']);
            $table->dropColumn('dropdown_page_id');
        });
    }
};
