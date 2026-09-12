<?php

use App\Models\Attendance;
use App\Models\DailySlotTracking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.employee')] #[Title('Employee Portal - TechMage')] class extends Component
{
    public function getTodayTracking(): ?DailySlotTracking
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        return DailySlotTracking::where('user_id', $user->id)
            ->whereDate('tracking_date', now()->toDateString())
            ->first();
    }

    public function clockIn(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $now = now();
        $today = $now->toDateString();

        // Ensure Attendance record exists
        Attendance::firstOrCreate(
            ['user_id' => $user->id, 'attendance_date' => $today],
            ['status' => 'present', 'clock_in_time' => $now]
        );

        // Ensure DailySlotTracking record exists
        $tracking = DailySlotTracking::firstOrCreate(
            ['user_id' => $user->id, 'tracking_date' => $today],
            ['slot1_checkin_time' => $now]
        );

        if (! $tracking->slot1_checkin_time) {
            $tracking->update(['slot1_checkin_time' => $now]);
        }

        session()->flash('attendance_status', 'Clocked in at '.$now->format('g:i A').'. Slot 2 check-in will be available after 1.5 hours.');
    }

    public function save2HrCheckin(): void
    {
        $tracking = $this->getTodayTracking();
        if (! $tracking || ! $tracking->slot1_checkin_time) {
            session()->flash('error', 'Please perform initial clock in first.');

            return;
        }

        $now = now();
        $checkinTime = $tracking->slot1_checkin_time;
        $elapsedMinutes = (int) $checkinTime->diffInMinutes($now);

        // Cannot perform checkin before 1.5 hrs (90 mins)
        if ($elapsedMinutes < 90) {
            session()->flash('error', 'Slot 2 check-in is disabled until 1.5 hours have elapsed. Currently elapsed: '.$elapsedMinutes.' mins.');

            return;
        }

        // Check if early (<90m - handled above) or exceeded (>150m = 2.5 hrs)
        $status = 'normal';
        $deviation = 0;
        $isFlagged = false;

        if ($elapsedMinutes > 150) {
            $status = 'exceeded';
            $deviation = $elapsedMinutes - 120;
            $isFlagged = true;
        }

        $tracking->update([
            'slot2_checkin_time' => $now,
            'slot2_timing_status' => $status,
            'slot2_deviation_minutes' => $deviation,
            'slot2_is_flagged' => $isFlagged,
        ]);

        if ($isFlagged) {
            session()->flash('attendance_status', "Slot 2 check-in saved! Exceeded 30-min window by {$deviation} mins (Flagged in RED to Admin).");
        } else {
            session()->flash('attendance_status', 'Slot 2 check-in completed on time at '.$now->format('g:i A'));
        }
    }

    public function startLunch(): void
    {
        $tracking = $this->getTodayTracking();
        if (! $tracking || ! $tracking->slot2_checkin_time) {
            session()->flash('error', 'Please complete your Slot 2 check-in before going for lunch.');

            return;
        }

        $now = now();
        $tracking->update(['lunch_start_time' => $now]);

        session()->flash('attendance_status', 'Lunch break started at '.$now->format('g:i A').'. Maximum duration is 1 hour.');
    }

    public function endLunchAndStartSlot3(): void
    {
        $tracking = $this->getTodayTracking();
        if (! $tracking || ! $tracking->lunch_start_time) {
            session()->flash('error', 'No active lunch break session found.');

            return;
        }

        $now = now();
        $lunchStart = $tracking->lunch_start_time;
        $durationMinutes = (int) $lunchStart->diffInMinutes($now);

        $isExceeded = $durationMinutes > 60;
        $exceededMinutes = $isExceeded ? ($durationMinutes - 60) : 0;

        $tracking->update([
            'lunch_end_time' => $now,
            'lunch_duration_minutes' => $durationMinutes,
            'lunch_exceeded' => $isExceeded,
            'lunch_exceeded_minutes' => $exceededMinutes,
            'slot3_start_time' => $now,
        ]);

        if ($isExceeded) {
            session()->flash('attendance_status', "Lunch ended. Exceeded 1-hour limit by {$exceededMinutes} mins! (Flagged in RED to Admin). 3rd Slot started.");
        } else {
            session()->flash('attendance_status', 'Lunch ended on time. 3rd Slot started at '.$now->format('g:i A'));
        }
    }

    public function startSlot3(): void
    {
        $tracking = $this->getTodayTracking();
        if (! $tracking) {
            session()->flash('error', 'No tracking record found for today.');

            return;
        }

        $now = now();
        $tracking->update(['slot3_start_time' => $now]);

        session()->flash('attendance_status', '3rd Working Slot started at '.$now->format('g:i A'));
    }

    public function saveSlot3AndClockOut(): void
    {
        $tracking = $this->getTodayTracking();
        if (! $tracking || ! $tracking->slot3_start_time) {
            session()->flash('error', '3rd Slot has not been started yet.');

            return;
        }

        $now = now();
        $slot3Start = $tracking->slot3_start_time;
        $workedMinutes = (int) $slot3Start->diffInMinutes($now);

        // 3rd slot expected duration is 2 hours (120 mins)
        // Exceeded by >30 mins = worked > 150 mins
        $status = 'normal';
        $deviation = 0;
        $isFlagged = false;

        if ($workedMinutes > 150) {
            $status = 'exceeded';
            $deviation = $workedMinutes - 120;
            $isFlagged = true;
        }

        $tracking->update([
            'slot3_end_time' => $now,
            'slot3_timing_status' => $status,
            'slot3_deviation_minutes' => $deviation,
            'slot3_is_flagged' => $isFlagged,
        ]);

        // Sync main attendance clock out
        $user = Auth::user();
        if ($user) {
            Attendance::where('user_id', $user->id)
                ->where('attendance_date', now()->toDateString())
                ->update(['clock_out_time' => $now]);
        }

        if ($isFlagged) {
            session()->flash('attendance_status', "3rd Slot completed & Clocked out! Exceeded 3rd slot by {$deviation} mins (>30m threshold). (Flagged in RED to Admin)");
        } else {
            session()->flash('attendance_status', '3rd Slot completed & Clocked out successfully at '.$now->format('g:i A'));
        }
    }

    public function render()
    {
        $tracking = $this->getTodayTracking();

        // Calculate timing and eligibility
        $elapsedMinutesSinceCheckin = 0;
        $is2HrCheckinEligible = false;
        $minutesRemainingFor2HrCheckin = 0;

        if ($tracking && $tracking->slot1_checkin_time && ! $tracking->slot2_checkin_time) {
            $elapsedMinutesSinceCheckin = (int) $tracking->slot1_checkin_time->diffInMinutes(now());
            if ($elapsedMinutesSinceCheckin >= 90) {
                $is2HrCheckinEligible = true;
            } else {
                $minutesRemainingFor2HrCheckin = 90 - $elapsedMinutesSinceCheckin;
            }
        }

        return view('employee.dashboard.dashboard', [
            'tracking' => $tracking,
            'elapsedMinutesSinceCheckin' => $elapsedMinutesSinceCheckin,
            'is2HrCheckinEligible' => $is2HrCheckinEligible,
            'minutesRemainingFor2HrCheckin' => $minutesRemainingFor2HrCheckin,
        ]);
    }
};
