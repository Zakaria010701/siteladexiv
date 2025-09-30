<?php

use App\Models\SystemResource;
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
        Schema::create('system_resource_dependables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(SystemResource::class)->constrained()->cascadeOnDelete();
            $table->morphs('dependable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_resource_dependables');
    }
};
