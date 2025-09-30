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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('filiale');
            $table->string('name');
            $table->string('abkuerzung');
            $table->string('verfuegbarkeit');
            $table->string('farbe');
            $table->dateTime('verfuegbarkeitsdatum');
            $table->string('kategorie');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
