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
        Schema::create('appointment_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Appointment::class, 'appointment_id')->constrained()->cascadeOnDelete();

            $table->string('status')->index();

            $table->decimal('base_total');
            $table->decimal('discount_total')->default(0);
            $table->decimal('net_total');
            $table->decimal('tax_total')->default(0);
            $table->decimal('gross_total');
            $table->decimal('paid_total')->default(0);

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_orders');
    }
};
