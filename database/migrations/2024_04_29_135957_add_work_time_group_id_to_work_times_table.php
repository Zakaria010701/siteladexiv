<?php

use App\Models\WorkTimeGroup;
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
        Schema::table('work_times', function (Blueprint $table) {
            $table->foreignIdFor(WorkTimeGroup::class, 'work_time_group_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropColumn('work_time_group_id');
        });
    }
};
