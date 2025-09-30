<?php

use App\Models\Customer;
use App\Models\Service;
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
        Schema::create('service_credits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Customer::class, 'customer_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Service::class, 'service_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('source');
            $table->nullableMorphs('usage');

            $table->decimal('price');
            $table->timestamp('used_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_credits');
    }
};
