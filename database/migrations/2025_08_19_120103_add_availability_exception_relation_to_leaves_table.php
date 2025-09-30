<?php

use App\Models\AvailabilityAbsence;
use App\Models\AvailabilityException;
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
        Schema::table('leaves', function (Blueprint $table) {
            $table->foreignIdFor(AvailabilityAbsence::class)->nullable()->after('processed_by_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(AvailabilityAbsence::class);
        });
    }
};
