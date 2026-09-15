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
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('leave_request_id')
                ->nullable()
                ->after('status')
                ->constrained('leave_requests')
                ->nullOnDelete();

            $table->string('leave_category')->nullable()->after('leave_request_id');
            $table->string('leave_payment_type')->nullable()->after('leave_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['leave_request_id']);
            $table->dropColumn(['leave_request_id', 'leave_category', 'leave_payment_type']);
        });
    }
};
