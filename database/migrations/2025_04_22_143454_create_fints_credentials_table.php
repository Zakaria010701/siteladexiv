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
        Schema::create('fints_credentials', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(\App\Models\Bank::class)->nullable();

            $table->string('bank_name');
            $table->string('bank_url')->nullable();
            $table->string('bank_port')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('bank_2fa')->nullable();
            $table->string('bank_2fa_device')->nullable();
            $table->string('fints_account')->nullable();
            
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fints_credentials');
    }
};
