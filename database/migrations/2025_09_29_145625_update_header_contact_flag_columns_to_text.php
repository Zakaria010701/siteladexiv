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
       Schema::table('header_contact', function (Blueprint $table) {
           $table->text('german_flag_icon')->nullable()->change();
           $table->text('english_flag_icon')->nullable()->change();
       });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
       Schema::table('header_contact', function (Blueprint $table) {
           $table->string('german_flag_icon')->nullable()->change();
           $table->string('english_flag_icon')->nullable()->change();
       });
   }
};
