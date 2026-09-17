<?php

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\DailySlotTracking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.employee')] #[Title('Employee Portal - TechMage')] class extends Component
{
    #[On('slot-updated')]
    public function refreshDashboard(): void
    {
        // Triggers component re-render when slot-updated event is dispatched
    }

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
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'attendance_date' => $today],
            ['status' => 'present', 'clock_in_time' => $now]
        );

        // Ensure DailySlotTracking record exists
        $tracking = DailySlotTracking::firstOrCreate(
            ['user_id' => $user->id, 'tracking_date' => $today],
            [
                'attendance_id' => $attendance->id,
                'slot1_checkin_time' => $now,
            ]
        );

        if (! $tracking->slot1_checkin_time || ! $tracking->attendance_id) {
            $tracking->update([
                'attendance_id' => $attendance->id,
                'slot1_checkin_time' => $now,
            ]);
        }

        $msg = 'Clocked in at '.$now->format('g:i A').'. Slot 2 check-in will be available after 2 hours.';
        session()->flash('attendance_status', $msg);
        $this->dispatch('toast-show', [
            'message' => $msg,
            'type' => 'success',
            'position' => 'top-right',
        ]);
        $this->dispatch('slot-updated');
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

        // Cannot perform checkin before 2 hrs (120 mins)
        if ($elapsedMinutes < 120) {
            session()->flash('error', 'Slot 2 check-in is disabled until 2 hours have elapsed. Currently elapsed: '.$elapsedMinutes.' mins.');

            return;
        }

        // Check if exceeded (>150m = 2.5 hrs, i.e., 30 mins after 120m)
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

        $this->dispatch('slot-updated');
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
        $this->dispatch('slot-updated');
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

        $this->dispatch('slot-updated');
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
        $this->dispatch('slot-updated');
    }

    public function saveSlot4AndClockOut(): void
    {
        $tracking = $this->getTodayTracking();
        if (! $tracking || ! $tracking->slot3_start_time) {
            session()->flash('error', '3rd Slot has not been started yet.');

            return;
        }

        $now = now();
        $slot3Start = $tracking->slot3_start_time;
        $workedMinutes = (int) $slot3Start->diffInMinutes($now);

        // Cannot perform 4th slot checkin before 90 mins after 3rd slot start
        if ($workedMinutes < 90) {
            session()->flash('error', '4th Slot check-in is disabled until 90 minutes have elapsed after 3rd slot start. Currently elapsed: '.$workedMinutes.' mins.');

            return;
        }

        // Exceeded by >30 mins after 90m = worked > 120 mins
        $status = 'normal';
        $deviation = 0;
        $isFlagged = false;

        if ($workedMinutes > 120) {
            $status = 'exceeded';
            $deviation = $workedMinutes - 90;
            $isFlagged = true;
        }

        $tracking->update([
            'slot3_end_time' => $now,
            'slot4_checkin_time' => $now,
            'slot4_timing_status' => $status,
            'slot4_deviation_minutes' => $deviation,
            'slot4_is_flagged' => $isFlagged,
        ]);

        // Sync main attendance clock out and active attendance log
        $user = Auth::user();
        if ($user) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('attendance_date', now()->toDateString())
                ->first();

            if ($attendance) {
                $attendance->update(['clock_out_time' => $now]);

                $activeLog = AttendanceLog::where('attendance_id', $attendance->id)
                    ->whereNull('clock_out_time')
                    ->first();

                if ($activeLog) {
                    $clockIn = $activeLog->clock_in_time;
                    $duration = $clockIn ? (int) $clockIn->diffInMinutes($now) : 0;
                    $activeLog->update([
                        'clock_out_time' => $now,
                        'duration_minutes' => $duration,
                    ]);
                }
            }
        }

        if ($isFlagged) {
            session()->flash('attendance_status', "4th Slot completed & Clocked out! Exceeded 90-min slot by {$deviation} mins (>30m threshold). (Flagged in RED to Admin)");
        } else {
            session()->flash('attendance_status', '4th Slot completed & Clocked out successfully at '.$now->format('g:i A'));
        }

        $this->dispatch('slot-updated');
    }

    public function saveSlot3AndClockOut(): void
    {
        $this->saveSlot4AndClockOut();
    }

    public function render()
    {
        $tracking = $this->getTodayTracking();

        // Calculate timing and eligibility for Slot 2 (available after 120m)
        $elapsedMinutesSinceCheckin = 0;
        $is2HrCheckinEligible = false;
        $minutesRemainingFor2HrCheckin = 0;

        if ($tracking && $tracking->slot1_checkin_time && ! $tracking->slot2_checkin_time) {
            $elapsedMinutesSinceCheckin = (int) $tracking->slot1_checkin_time->diffInMinutes(now());
            if ($elapsedMinutesSinceCheckin >= 120) {
                $is2HrCheckinEligible = true;
            } else {
                $minutesRemainingFor2HrCheckin = 120 - $elapsedMinutesSinceCheckin;
            }
        }

        // Calculate timing and eligibility for Slot 4 (available after 90m from 3rd slot start)
        $elapsedMinutesSlot3 = 0;
        $is4thSlotEligible = false;
        $minutesRemainingFor4thSlot = 0;

        if ($tracking && $tracking->slot3_start_time && ! $tracking->slot4_checkin_time) {
            $elapsedMinutesSlot3 = (int) $tracking->slot3_start_time->diffInMinutes(now());
            if ($elapsedMinutesSlot3 >= 90) {
                $is4thSlotEligible = true;
            } else {
                $minutesRemainingFor4thSlot = 90 - $elapsedMinutesSlot3;
            }
        }

        return view('employee.dashboard.dashboard', [
            'tracking' => $tracking,
            'elapsedMinutesSinceCheckin' => $elapsedMinutesSinceCheckin,
            'is2HrCheckinEligible' => $is2HrCheckinEligible,
            'minutesRemainingFor2HrCheckin' => $minutesRemainingFor2HrCheckin,
            'elapsedMinutesSlot3' => $elapsedMinutesSlot3,
            'is4thSlotEligible' => $is4thSlotEligible,
            'minutesRemainingFor4thSlot' => $minutesRemainingFor4thSlot,
        ]);
    }
};
