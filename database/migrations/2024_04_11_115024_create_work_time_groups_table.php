<?php

use App\Models\Branch;
use App\Models\Room;
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
        Schema::create('work_time_groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->foreignIdFor(Branch::class, 'branch_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Room::class, 'room_id')->constrained()->cascadeOnDelete();

            $table->string('type');
            $table->time('start');
            $table->time('end');

            $table->date('repeat_from');
            $table->date('repeat_till');
            $table->string('repeat_step');
            $table->integer('repeat_every');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_time_groups');
    }
};
