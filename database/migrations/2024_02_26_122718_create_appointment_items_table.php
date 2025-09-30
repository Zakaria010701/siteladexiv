<?php

use App\Models\Appointment;
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
        Schema::create('appointment_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Appointment::class)->constrained()->cascadeOnDelete();
            $table->nullableMorphs('purchasable');

            $table->string('description');
            $table->string('note')->nullable();

            $table->decimal('unit_price');
            $table->decimal('quantity')->default(1);
            $table->decimal('used')->default(1);

            $table->decimal('discount_total')->default(0);
            $table->decimal('sub_total');

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_items');
    }
};
