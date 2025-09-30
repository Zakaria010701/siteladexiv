<?php

use App\Models\SystemResourceType;
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
        Schema::table('system_resource_types', function (Blueprint $table) {
            $table->boolean('depends_on_branch')->default(false)->after('allow_multiple');
            $table->boolean('depends_on_room')->default(false)->after('depends_on_branch');
            $table->boolean('depends_on_category')->default(false)->after('depends_on_room');
            $table->boolean('depends_on_user')->default(false)->after('depends_on_category');
        });

        Schema::create('system_resource_type_dependency', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(SystemResourceType::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SystemResourceType::class, 'dependency_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_resource_types', function (Blueprint $table) {
            $table->dropColumn('depends_on_branch');
            $table->dropColumn('depends_on_room');
            $table->dropColumn('depends_on_category');
            $table->dropColumn('depends_on_user');
        });

        Schema::drop('system_resource_type_dependency');
    }
};
