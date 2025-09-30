<?php

use App\Models\Customer;
use App\Models\User;
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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->nullableMorphs('notable');
            $table->foreignIdFor(User::class, 'user_id')->nullable()->constrained();
            $table->foreignIdFor(Customer::class, 'customer_id')->nullable()->constrained()->cascadeOnDelete();

            $table->text('content');
            $table->boolean('is_important')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
