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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();

            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->boolean('severely_disabled')->default(false);
            $table->string('place_of_birth')->nullable();
            $table->string('country')->nullable();
            $table->string('nationality')->nullable();

            $table->string('social_security_number')->nullable();
            $table->string('health_insurance')->nullable();
            $table->string('health_insurance_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('tax_class')->nullable();
            $table->string('religious_affiliation')->nullable();
            $table->boolean('children')->default(false);

            $table->date('start_of_employment')->nullable();
            $table->string('occupation')->nullable();
            $table->integer('weekly_hours')->nullable();
            $table->integer('probationary_period')->nullable();
            $table->boolean('no_leaves_during_probationary_period')->default(false);
            $table->boolean('other_occupations')->default(false);
            $table->string('other_occupation_employer')->nullable();
            $table->string('other_occupation_weekly_hours')->nullable();
            $table->decimal('wage')->nullable();
            $table->string('wage_type')->nullable();
            $table->boolean('limited')->default(false);
            $table->decimal('second_wage')->nullable();
            $table->string('second_wage_type')->nullable();
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
