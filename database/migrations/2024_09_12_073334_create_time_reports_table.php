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
        Schema::create('time_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\User::class, 'user_id')->constrained();
            $table->date('date')->index();

            $table->integer('target_minutes')->default(0);

            $table->dateTime('work_time_start')->nullable()->index();
            $table->dateTime('work_time_end')->nullable()->index();
            $table->integer('work_time_minutes')->default(0);

            $table->dateTime('time_in')->nullable()->index();
            $table->string('time_in_status')->nullable()->index();
            $table->dateTime('time_out')->nullable()->index();
            $table->string('time_out_status')->nullable()->index();
            $table->integer('total_minutes')->default(0);

            $table->dateTime('real_time_in')->nullable()->index();
            $table->dateTime('real_time_out')->nullable()->index();
            $table->integer('real_total_minutes')->default(0);

            $table->integer('break_minutes')->default(0);

            $table->integer('actual_minutes')->default(0);

            $table->integer('manual_minutes')->default(0);

            $table->integer('overtime_minutes')->default(0);
            $table->integer('uncapped_overtime_minutes')->default(0);
            $table->boolean('is_overtime_capped')->default(true);

            $table->string('leave_type')->nullable()->index();

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
        Schema::dropIfExists('time_reports');
    }
};
