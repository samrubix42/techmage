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
        Schema::create('daily_slot_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tracking_date');

            // Slot 1 (Shift Clock-In)
            $table->timestamp('slot1_checkin_time')->nullable();

            // Slot 2 Check-In (Timing only)
            $table->timestamp('slot2_checkin_time')->nullable();
            $table->string('slot2_timing_status')->default('normal'); // 'normal', 'exceeded'
            $table->integer('slot2_deviation_minutes')->default(0);
            $table->boolean('slot2_is_flagged')->default(false);

            // Lunch Break Tracking (1 Hour)
            $table->timestamp('lunch_start_time')->nullable();
            $table->timestamp('lunch_end_time')->nullable();
            $table->integer('lunch_duration_minutes')->nullable();
            $table->boolean('lunch_exceeded')->default(false);
            $table->integer('lunch_exceeded_minutes')->default(0);

            // 3rd Slot Tracking & Shift End (Timing only)
            $table->timestamp('slot3_start_time')->nullable();
            $table->timestamp('slot3_end_time')->nullable();
            $table->string('slot3_timing_status')->default('normal'); // 'normal', 'early', 'exceeded'
            $table->integer('slot3_deviation_minutes')->default(0);
            $table->boolean('slot3_is_flagged')->default(false);

            $table->timestamps();

            $table->unique(['user_id', 'tracking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_slot_trackings');
    }
};
