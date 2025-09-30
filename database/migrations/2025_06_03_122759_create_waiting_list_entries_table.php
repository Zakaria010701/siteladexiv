<?php

use App\Models\Branch;
use App\Models\Category;
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
        Schema::create('waiting_list_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Category::class)->constrained();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->string('appointment_type');
            $table->text('note')->nullable();
            $table->date('wish_date')->nullable();
            $table->date('wish_date_till')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_list_entries');
    }
};
