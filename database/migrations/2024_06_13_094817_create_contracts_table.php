<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\Customer::class, 'customer_id');
            $table->foreignIdFor(\App\Models\Appointment::class, 'appointment_id')->nullable();
            $table->foreignIdFor(\App\Models\Payment::class, 'payment_id')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'user_id')->nullable();

            $table->date('date');
            $table->string('type');
            $table->text('description')->nullable();
            $table->decimal('price');
            $table->integer('treatment_count');

            $table->json('meta')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');

    }
};
