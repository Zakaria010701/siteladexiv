<?php

use App\Models\NotificationTemplate;
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
        Schema::create('appointment_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(NotificationTemplate::class, 'notification_template_id');
            $table->integer('days_before');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_reminder_settings');
    }
};
