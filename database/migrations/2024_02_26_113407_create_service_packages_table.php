<?php

use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
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
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignIdFor(Category::class, 'category_id')->constrained();
            $table->string('name')->index();
            $table->string('short_code')->index();
        });

        Schema::create('service_service_package', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Service::class, 'service_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ServicePackage::class, 'service_package_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_packages');

        Schema::dropIfExists('service_service_package');
    }
};
