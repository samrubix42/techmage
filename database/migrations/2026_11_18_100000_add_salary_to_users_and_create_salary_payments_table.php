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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('salary', 12, 2)->default(0)->after('saturday_off_policy');
        });

        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Salary month (always stored as the 1st of the month)
            $table->date('salary_month');

            // Snapshot of the calculation at the time of payment
            $table->decimal('monthly_salary', 12, 2);
            $table->unsignedSmallInteger('total_days');
            $table->unsignedSmallInteger('week_off_days');
            $table->unsignedSmallInteger('holiday_days');
            $table->unsignedSmallInteger('working_days');
            $table->decimal('present_days', 5, 1);
            $table->decimal('paid_leave_days', 5, 1);
            $table->decimal('unpaid_leave_days', 5, 1);
            $table->decimal('absent_days', 5, 1);
            $table->decimal('payable_days', 5, 1);
            $table->decimal('per_day_salary', 12, 2);
            $table->decimal('calculated_salary', 12, 2);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2);

            $table->string('payment_method')->default('bank_transfer'); // cash, bank_transfer, upi, cheque
            $table->date('payment_date');
            $table->text('notes')->nullable();

            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'salary_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salary');
        });
    }
};
