<?php

use App\Models\AvailabilityType;
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
        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->foreignIdFor(AvailabilityType::class)->nullable()->index()->after('room_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_exceptions', function (Blueprint $table) {
            $table->dropForeignIdFor(AvailabilityType::class);
        });
    }
};
