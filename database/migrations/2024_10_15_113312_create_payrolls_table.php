<?php

use App\Models\Payroll;
use App\Models\TimeReport;
use App\Models\User;
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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->foreignIdFor(TimeReport::class, 'time_report_id')->nullable()->constrained();
            $table->foreignIdFor(Payroll::class, 'previous_id')->nullable()->constrained('payrolls');

            $table->date('from');
            $table->date('till');

            $table->integer('minutes')->default(0);
            $table->decimal('hourly_wage')->default(0);
            $table->decimal('payment')->default(0);
            $table->decimal('extra_payment')->default(0);

            $table->decimal('prev_balance')->default(0);
            $table->decimal('payout')->default(0);
            $table->decimal('current_balance')->default(0);

            $table->json('meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
