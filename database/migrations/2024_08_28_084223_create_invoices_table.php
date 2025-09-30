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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->morphs('recipient');
            $table->nullableMorphs('source');

            $table->string('type')->index();
            $table->string('status')->index();

            $table->string('series')->index();
            $table->integer('sequence')->index();
            $table->string('invoice_number')->index();

            $table->date('invoice_date')->index();
            $table->date('due_date')->nullable()->index();

            $table->decimal('base_total');
            $table->decimal('discount_total');
            $table->decimal('net_total');
            $table->decimal('tax_total');
            $table->decimal('gross_total');
            $table->decimal('paid_total');

            $table->text('header')->nullable();
            $table->text('footer')->nullable();

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
