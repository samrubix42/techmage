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
        Schema::table('daily_task_reports', function (Blueprint $table) {
            $table->boolean('is_checked')->default(false)->after('title_description');
            $table->timestamp('checked_at')->nullable()->after('is_checked');
            $table->foreignId('checked_by')->nullable()->after('checked_at')->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable()->after('checked_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_task_reports', function (Blueprint $table) {
            $table->dropForeign(['checked_by']);
            $table->dropColumn(['is_checked', 'checked_at', 'checked_by', 'admin_notes']);
        });
    }
};
