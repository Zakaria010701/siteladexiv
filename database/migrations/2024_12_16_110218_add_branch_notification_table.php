<?php

use App\Models\Branch;
use App\Models\NotificationTemplate;
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
        Schema::create('branch_notification_template', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Branch::class, 'branch_id')->constrained();
            $table->foreignIdFor(NotificationTemplate::class, 'notification_template_id')->constrained();
        });

        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Branch::class, 'branch_id');
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_notification_template');

        Schema::table('notification_templates', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Branch::class, 'branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('content')->nullable(false)->change();
        });
    }
};
