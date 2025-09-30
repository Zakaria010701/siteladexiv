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
        Schema::create('time_report_overviews', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\User::class, 'user_id')->constrained();

            $table->date('date')->index();

            $table->integer('target_minutes')->default(0);
            $table->integer('work_time_minutes')->default(0);
            $table->integer('total_minutes')->default(0);
            $table->integer('real_total_minutes')->default(0);
            $table->integer('actual_minutes')->default(0);

            $table->integer('overtime_minutes')->default(0);
            $table->integer('uncapped_overtime_minutes')->default(0);
            $table->integer('carry_overtime_minutes')->default(0);
            $table->integer('manual_overtime_minutes')->default(0);

            $table->integer('leave_days')->default(0);
            $table->integer('sick_days')->default(0);
            $table->integer('vacation_days')->default(0);
            $table->integer('carry_vacation_days')->default(0);
            $table->integer('manual_vacation_days')->default(0);

            $table->timestamp('edited_at')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'edited_by_id')->nullable()->constrained('users', 'id');

            $table->timestamp('controlled_at')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'controlled_by_id')->nullable()->constrained('users', 'id');

            $table->text('note')->nullable();
            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_report_overviews');
    }
};
