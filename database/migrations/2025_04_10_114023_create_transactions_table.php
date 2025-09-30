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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\Account::class)->constrained();
            $table->foreignIdFor(\App\Models\Bank::class)->constrained();

            $table->nullableMorphs('bookable');

            $table->date('date')->index();

            $table->decimal('amount', 10, 2);

            $table->string('description', 1024)->nullable();

            $table->string('type')->index();
            $table->string('status')->index();

            $table->string('hash')->unique()->index();

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
