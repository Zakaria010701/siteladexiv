<?php

use App\Models\ContractService;
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
        Schema::table('service_credits', function (Blueprint $table) {
            $table->foreignIdFor(ContractService::class, 'contract_service_id')->nullable()->after('contract_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_credits', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(ContractService::class);
        });
    }
};
