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
       Schema::create('header_contact', function (Blueprint $table) {
           $table->id();
           $table->timestamps();

           // Welcome text (from screenshot 1)
           $table->string('welcome_text')->default('Welcome to our website');

           // Contact information (from screenshot 2)
           $table->string('phone')->nullable();
           $table->string('email')->nullable();
           $table->text('address')->nullable();

           // Social media links
           $table->string('facebook_url')->nullable();
           $table->string('instagram_url')->nullable();
           $table->string('tiktok_url')->nullable();

           // Language flags
           $table->text('german_flag_icon')->nullable(); // SVG or icon class
           $table->text('english_flag_icon')->nullable(); // SVG or icon class

           // Additional settings
           $table->boolean('is_active')->default(true);
           $table->integer('position')->default(0);
       });
   }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_contact');
    }
};
