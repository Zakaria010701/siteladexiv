<?php

use App\Models\Customer;
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
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Customer::class, 'customer_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('subject');

            $table->string('channel')->nullable();
            $table->string('title')->nullable();
            $table->string('message')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_contacts');
    }
};
