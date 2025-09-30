<?php

use App\Models\Appointment;
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
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('default_price')->default(0)->after('description');
            $table->decimal('discount_percentage')->default(0)->after('default_price');
            $table->decimal('sub_total')->default(0)->after('discount_percentage');
            $table->foreignIdFor(Appointment::class, 'credited_appointment_id')->nullable()->after('appointment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('default_price');
            $table->dropColumn('discount_percentage');
            $table->dropColumn('sub_total');
            $table->dropColumn('credited_appointment_id');
        });
    }
};
