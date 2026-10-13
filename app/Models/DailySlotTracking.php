<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySlotTracking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tracking_date' => 'date',
            'slot1_checkin_time' => 'datetime',
            'slot2_checkin_time' => 'datetime',
            'lunch_start_time' => 'datetime',
            'lunch_end_time' => 'datetime',
            'slot3_start_time' => 'datetime',
            'slot3_end_time' => 'datetime',
            'slot4_checkin_time' => 'datetime',
            'slot2_is_flagged' => 'boolean',
            'lunch_exceeded' => 'boolean',
            'slot3_is_flagged' => 'boolean',
            'slot4_is_flagged' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Calculate net worked minutes based on slot tracking minus lunch duration.
     */
    public function getWorkedMinutes(?Attendance $attendance = null, ?CarbonInterface $referenceNow = null): int
    {
        $firstLogClockIn = $attendance?->logs?->first()?->clock_in_time;
        $startTime = $this->slot1_checkin_time ?? $firstLogClockIn ?? $attendance?->clock_in_time;
        if ($firstLogClockIn && $startTime && $firstLogClockIn->lt($startTime)) {
            $startTime = $firstLogClockIn;
        }

        if (! $startTime) {
            return 0;
        }

        $lastLogClockOut = $attendance?->logs?->whereNotNull('clock_out_time')->last()?->clock_out_time;
        $endTime = $this->slot4_checkin_time ?? $this->slot3_end_time ?? $attendance?->clock_out_time ?? $lastLogClockOut;

        $isToday = $this->tracking_date ? $this->tracking_date->isToday() : false;

        if (! $endTime) {
            if ($isToday) {
                $endTime = $referenceNow ?? now();
            } else {
                $endTime = $this->slot3_start_time
                    ?? $this->lunch_end_time
                    ?? $this->lunch_start_time
                    ?? $this->slot2_checkin_time
                    ?? $startTime;
            }
        }

        if ($endTime->lt($startTime)) {
            return 0;
        }

        $totalSpanMinutes = (int) $startTime->diffInMinutes($endTime);

        // Determine lunch duration in minutes to subtract ("minus lunch hr")
        $lunchMinutes = 0;
        if ($this->lunch_duration_minutes !== null && $this->lunch_duration_minutes > 0) {
            $lunchMinutes = (int) $this->lunch_duration_minutes;
        } elseif ($this->lunch_start_time && $this->lunch_end_time) {
            $lunchMinutes = (int) $this->lunch_start_time->diffInMinutes($this->lunch_end_time);
        } elseif ($this->lunch_start_time && $this->slot3_start_time) {
            $lunchMinutes = (int) $this->lunch_start_time->diffInMinutes($this->slot3_start_time);
        } elseif ($this->lunch_start_time) {
            if ($isToday && now()->gt($this->lunch_start_time)) {
                $lunchRef = $endTime->lt(now()) ? $endTime : now();
                $lunchMinutes = (int) $this->lunch_start_time->diffInMinutes($lunchRef);
            } else {
                $lunchMinutes = 60;
            }
        } elseif ($this->slot3_start_time || $this->slot4_checkin_time || $totalSpanMinutes >= 300) {
            // Reached afternoon slots or shift >= 5 hours: standard 1-hour lunch break (60 mins) deducted
            $lunchMinutes = 60;
        }

        $netMinutes = max(0, $totalSpanMinutes - $lunchMinutes);

        // Fallback: If attendance logs has longer duration and slot4 wasn't explicitly clocked out
        if ($attendance && $attendance->relationLoaded('logs') && $attendance->logs->isNotEmpty()) {
            $logsDuration = 0;
            foreach ($attendance->logs as $log) {
                if ($log->clock_out_time) {
                    $logsDuration += $log->duration_minutes ?: (int) $log->clock_in_time->diffInMinutes($log->clock_out_time);
                }
            }
            if ($logsDuration > $netMinutes && ! $this->slot4_checkin_time) {
                return $logsDuration;
            }
        }

        return $netMinutes;
    }

    /**
     * Calculate break minutes for a given tracking and attendance record.
     */
    public static function calculateBreaksMinutesForDay(?DailySlotTracking $tracking, ?Attendance $attendance): int
    {
        if ($tracking && $tracking->lunch_duration_minutes !== null) {
            return (int) $tracking->lunch_duration_minutes;
        }

        if ($tracking && $tracking->lunch_start_time && $tracking->lunch_end_time) {
            return (int) $tracking->lunch_start_time->diffInMinutes($tracking->lunch_end_time);
        }

        $logs = $attendance ? ($attendance->relationLoaded('logs') ? $attendance->logs : $attendance->logs()->get()) : collect();
        $totalBreaks = 0;
        $lastClockOut = null;
        foreach ($logs as $log) {
            $clockIn = $log->clock_in_time;
            $clockOut = $log->clock_out_time;
            if ($lastClockOut && $clockIn) {
                $break = (int) $lastClockOut->diffInMinutes($clockIn);
                if ($break > 0) {
                    $totalBreaks += $break;
                }
            }
            if ($clockOut) {
                $lastClockOut = $clockOut;
            }
        }

        return $totalBreaks;
    }

    /**
     * Compute worked minutes for a specific day given optional tracking and attendance models.
     */
    public static function calculateWorkedMinutesForDay(?DailySlotTracking $tracking, ?Attendance $attendance, ?CarbonInterface $referenceNow = null): int
    {
        if ($tracking) {
            return $tracking->getWorkedMinutes($attendance, $referenceNow);
        }

        if (! $attendance) {
            return 0;
        }

        $logs = $attendance->relationLoaded('logs') ? $attendance->logs : $attendance->logs()->get();
        if ($logs->isNotEmpty()) {
            $totalWorkedMinutes = 0;
            $isToday = $attendance->attendance_date ? $attendance->attendance_date->isToday() : false;

            foreach ($logs as $log) {
                if ($log->clock_out_time) {
                    $totalWorkedMinutes += $log->duration_minutes ?: (int) $log->clock_in_time->diffInMinutes($log->clock_out_time);
                } elseif ($log->clock_in_time) {
                    $ref = $isToday ? ($referenceNow ?? now()) : $log->clock_in_time;
                    $totalWorkedMinutes += (int) $log->clock_in_time->diffInMinutes($ref);
                }
            }

            return $totalWorkedMinutes;
        }

        if ($attendance->clock_in_time) {
            $clockOut = $attendance->clock_out_time;
            if (! $clockOut) {
                $isToday = $attendance->attendance_date ? $attendance->attendance_date->isToday() : false;
                $clockOut = $isToday ? ($referenceNow ?? now()) : $attendance->clock_in_time;
            }

            if ($clockOut->gte($attendance->clock_in_time)) {
                return (int) $attendance->clock_in_time->diffInMinutes($clockOut);
            }
        }

        return 0;
    }

    /**
     * Format minutes to "Xh Ym" string format.
     */
    public static function formatMinutesToHours(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return "{$hours}h {$mins}m";
    }

    /**
     * Check if worked time is less than 8 hours (480 minutes).
     */
    public static function isUnderEightHours(int $minutes): bool
    {
        return $minutes < 480;
    }
}
