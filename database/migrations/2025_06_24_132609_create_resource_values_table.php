<?php

use App\Models\ResourceField;
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
        Schema::create('resource_values', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(SystemResource::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ResourceField::class)->index()->constrained()->cascadeOnDelete();

            $table->longText('value');

            $table->unique(['system_resource_id', 'resource_field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_values');
    }
};
