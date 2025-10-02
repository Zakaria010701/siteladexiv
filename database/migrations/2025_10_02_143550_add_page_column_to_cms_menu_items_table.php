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
            $table->unsignedBigInteger('page')->nullable()->after('url');
            $table->foreign('page')->references('id')->on('cms_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_menu_items', function (Blueprint $table) {
            $table->dropForeign(['page']);
            $table->dropColumn('page');
        });
    }
};
