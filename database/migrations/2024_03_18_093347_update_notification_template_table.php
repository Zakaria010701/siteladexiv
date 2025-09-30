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
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropColumn('channel');

            $table->boolean('enable_mail')->default(true);
            $table->boolean('enable_sms')->default(false);
            $table->text('sms_content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->string('channel')->index();

            $table->dropColumn('enable_mail');
            $table->dropColumn('enable_sms');
            $table->dropColumn('sms_content');
        });
    }
};
