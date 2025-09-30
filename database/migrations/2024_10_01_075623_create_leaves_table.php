<?php

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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(User::class)->constrained();

            $table->string('leave_type')->index();
            $table->date('from')->index();
            $table->date('till')->index();

            $table->foreignIdFor(User::class, 'processed_by_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable()->index();
            $table->timestamp('denied_at')->nullable()->index();

            $table->text('user_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
