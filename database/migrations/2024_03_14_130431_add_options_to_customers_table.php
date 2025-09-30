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
            $table->boolean('no_notifications')->default(false);
            $table->boolean('no_emails')->default(false);
            $table->boolean('no_newsletters')->default(false);
            $table->boolean('no_phone_calls')->default(false);
            $table->boolean('no_sms')->default(false);
            $table->boolean('allows_picture_usage')->default(false);
            $table->boolean('no_further_appointments')->default(false);
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_difficult')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('no_notifications');
            $table->dropColumn('no_emails');
            $table->dropColumn('no_newsletters');
            $table->dropColumn('no_phone_calls');
            $table->dropColumn('no_sms');
            $table->dropColumn('allows_picture_usage');
            $table->dropColumn('no_further_appointments');
            $table->dropColumn('is_vip');
            $table->dropColumn('is_difficult');
        });
    }
};
