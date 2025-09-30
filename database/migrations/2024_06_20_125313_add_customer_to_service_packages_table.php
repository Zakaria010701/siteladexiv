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
        Schema::table('service_packages', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Customer::class, 'customer_id')->nullable()->after('category_id')->constrained();
            $table->decimal('discount_percentage')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_packages', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\Customer::class);
            $table->dropColumn('discount_percentage');
            $table->dropColumn('discount');
            $table->dropColumn('price');
        });
    }
};
