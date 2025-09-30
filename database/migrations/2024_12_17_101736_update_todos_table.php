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
        Schema::dropIfExists('todos');

        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('priority')->index();
            $table->string('status')->index();
            $table->date('due_date')->nullable();
            $table->text('description');
        });

        Schema::create('todo_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\Todo::class, 'todo_id')->constrained();
            $table->foreignIdFor(\App\Models\User::class, 'user_id')->constrained();
        });

        Schema::create('todoables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\Todo::class, 'todo_id')->constrained();
            $table->morphs('todoable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');

        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->integer('priority');
            $table->date('due_date')->nullable();
            $table->string('category')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'assigned_to')->index();
            $table->foreignIdFor(\App\Models\Customer::class, 'client')->nullable()->default(null);
            $table->string('description');
            $table->timestamps();
            $table->string('status')->nullable();
        });
    }
};
