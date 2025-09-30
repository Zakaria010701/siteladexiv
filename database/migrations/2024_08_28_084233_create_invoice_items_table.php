<?php

use App\Models\Invoice;
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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(Invoice::class, 'invoice_id')->constrained();
            $table->nullableMorphs('invoicable');

            $table->string('title');
            $table->string('description');

            $table->decimal('quantity');
            $table->string('unit');

            $table->decimal('unit_price');
            $table->decimal('tax_percentage');
            $table->decimal('tax');
            $table->decimal('sub_total');

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
