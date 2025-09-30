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
        Schema::table('availabilities', function (Blueprint $table) {
            $table->string('title')->nullable()->after('planable_id');
            $table->text('description')->nullable()->after('title');
            $table->string('color')->default('#3788d8')->after('description');
            $table->boolean('is_hidden')->default(false)->after('color');
            $table->boolean('is_all_day')->default(false)->after('is_hidden');
            $table->boolean('is_background')->default(false)->after('is_all_day');
            $table->boolean('is_background_inverted')->default(false)->after('is_background');
            $table->string('availability_type_id')->nullable()->after('is_background_inverted');
            $table->integer('target_minutes')->nullable()->after('availability_type_id');
            $table->json('shifts')->nullable()->after('target_minutes');
            $table->string('repeat')->nullable()->after('shifts');
            $table->integer('weekday')->nullable()->after('repeat');
            $table->string('type')->default('availability')->after('weekday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description',
                'color',
                'is_hidden',
                'is_all_day',
                'is_background',
                'is_background_inverted',
                'availability_type_id',
                'target_minutes',
                'shifts',
                'repeat',
                'weekday',
                'type'
            ]);
        });
    }
};