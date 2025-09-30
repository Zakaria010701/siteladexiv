<?php

use App\Models\User;
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
            $table->foreignIdFor(User::class, 'done_by_id')->nullable()->constrained('users')->after('user_id');

            $table->integer('next_appointment_in')->nullable();
            $table->string('next_appointment_step')->nullable();
            $table->date('next_appointment_date')->nullable();
            $table->timestamp('next_appointment_reminder_sent_at')->nullable();

            $table->timestamp('reminder_sent_at')->nullable()->after('done_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('done_by_id');
            $table->dropColumn('next_appointment_in');
            $table->dropColumn('next_appointment_step');
            $table->dropColumn('next_appointment_date');
            $table->dropColumn('next_appointment_reminder_sent_at');
            $table->dropColumn('reminder_sent_at');
        });
    }
};
