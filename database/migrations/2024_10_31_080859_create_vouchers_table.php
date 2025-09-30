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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\Customer::class, 'customer_id')->nullable()->constrained();

            $table->foreignIdFor(\App\Models\Customer::class, 'purchaser_id')->nullable()->constrained('customers');

            $table->integer('voucher_nr');

            $table->decimal('amount')->default(0.00);

            $table->text('description')->nullable();

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
