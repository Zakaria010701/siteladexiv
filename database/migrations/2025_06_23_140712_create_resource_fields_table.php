<?php

use App\Models\SystemResourceType;
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
        Schema::create('resource_fields', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(SystemResourceType::class)->constrained()->cascadeOnDelete();

            $table->string('name')->index();
            $table->string('type')->index();

            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('placeholder')->nullable();

            $table->json('options')->nullable();

            $table->longText('default_value')->nullable();

            $table->boolean('required')->default(false);
            $table->boolean('disabled')->default(false);
            $table->integer('order')->default(1);

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_fields');
    }
};
