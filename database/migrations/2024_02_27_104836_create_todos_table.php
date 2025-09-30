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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->integer('priority');
            $table->date('due_date')->nullable();
            $table->string('category')->nullable();
            $table->foreignIdFor(User::class, 'assigned_to')->index();
            $table->foreignIdFor(Customer::class, 'client')->nullable()->default(null);
            $table->string('description');
            $table->timestamps();
            $table->string('status')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
