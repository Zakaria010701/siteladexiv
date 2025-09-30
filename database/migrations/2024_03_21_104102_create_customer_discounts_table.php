<?php

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
        Schema::create('customer_discounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\Customer::class, 'customer_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Discount::class, 'source_id')->nullable()->constrained('discounts');

            $table->string('description');
            $table->decimal('percentage')->nullable();
            $table->decimal('amount')->nullable();
        });

        Schema::create('customer_discount_service', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\CustomerDiscount::class, 'customer_discount_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Service::class, 'service_id')->constrained()->cascadeOnDelete();

            $table->decimal('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_discounts');
        Schema::dropIfExists('customer_discount_service');
    }
};
