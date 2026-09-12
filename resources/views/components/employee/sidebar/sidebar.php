<?php

use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public function clockIn(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $today = now()->toDateString();
        $now = now();

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => $user->id,
                'attendance_date' => $today,
            ],
            [
                'status' => 'present',
                'clock_in_time' => $now,
                'slot1_start_time' => $now,
            ]
        );

        if (! $attendance->clock_in_time || ! $attendance->slot1_start_time) {
            $attendance->update([
                'clock_in_time' => $attendance->clock_in_time ?? $now,
                'slot1_start_time' => $attendance->slot1_start_time ?? $now,
                'status' => 'present',
            ]);
        }

        $activeLog = AttendanceLog::where('attendance_id', $attendance->id)
            ->whereNull('clock_out_time')
            ->first();

        if (! $activeLog) {
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'clock_in_time' => $now,
            ]);

            session()->flash('attendance_status', 'Clocked in at '.$now->format('g:i A'));
        }
    }

    public function clockOut(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $today = now()->toDateString();
        $now = now();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        if ($attendance) {
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

            $attendance->update([
                'clock_out_time' => $now,
            ]);

            session()->flash('attendance_status', 'Clocked out at '.$now->format('g:i A'));
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        $user = Auth::user();
        $todayAttendance = null;
        $activeLog = null;
        $logs = collect();

        if ($user) {
            $todayAttendance = Attendance::with(['logs' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }])
                ->where('user_id', $user->id)
                ->where('attendance_date', now()->toDateString())
                ->first();

            if ($todayAttendance) {
                $logs = $todayAttendance->logs;
                $activeLog = $logs->whereNull('clock_out_time')->first();
            }
        }

        return view('components.employee.sidebar.sidebar', [
            'todayAttendance' => $todayAttendance,
            'todayLogs' => $logs,
            'activeLog' => $activeLog,
            'todayDateFormatted' => now()->format('D, M d, Y'),
        ]);
    }
};
