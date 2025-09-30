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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('creditable_type')->nullable()->after('reference_id');
            $table->bigInteger('creditable_id')->unsigned()->nullable()->after('creditable_type');
            $table->index(["creditable_type", "creditable_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropMorphs('creditable');
        });
    }
};
