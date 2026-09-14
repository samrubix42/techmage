<?php

use App\Models\Attendance;
use App\Models\DailySlotTracking;
use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Employee Working Hours & Slots - TechMage')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedDate = '';

    public string $departmentFilter = '';

    public string $statusFilter = 'all';

    public ?int $selectedUserId = null;

    public bool $showDetailModal = false;

    #[On('slot-updated')]
    public function refreshComponent(): void
    {
        // Live reload if employee clock action happens
    }

    public function mount(): void
    {
        $this->selectedDate = now()->toDateString();
    }

    public function setToday(): void
    {
        $this->selectedDate = now()->toDateString();
        $this->resetPage();
    }

    public function setYesterday(): void
    {
        $this->selectedDate = now()->subDay()->toDateString();
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedDate(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedDate = now()->toDateString();
        $this->departmentFilter = '';
        $this->statusFilter = 'all';
        $this->resetPage();
    }

    public function openModal(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->showDetailModal = true;
    }

    public function closeModal(): void
    {
        $this->selectedUserId = null;
        $this->showDetailModal = false;
    }

    /**
     * Compute working hours stats, clock-in/out logs, and breaks for a specific user and date.
     */
    public function getUserStats(User $user, string $date): array
    {
        $attendance = Attendance::with(['logs' => function ($q) {
            $q->orderBy('clock_in_time', 'asc');
        }])
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', $date)
            ->first();

        $tracking = DailySlotTracking::where('user_id', $user->id)
            ->whereDate('tracking_date', $date)
            ->first();

        $logs = $attendance ? $attendance->logs : collect();

        $totalWorkedMinutes = 0;
        $activeSession = null;
        $totalBreaksMinutes = 0;
        $lastClockOut = null;

        foreach ($logs as $log) {
            $clockIn = $log->clock_in_time;
            $clockOut = $log->clock_out_time;

            if ($lastClockOut && $clockIn) {
                $breakDuration = (int) $lastClockOut->diffInMinutes($clockIn);
                if ($breakDuration > 0) {
                    $totalBreaksMinutes += $breakDuration;
                }
            }

            if ($clockOut) {
                $duration = $log->duration_minutes ?: (int) $clockIn->diffInMinutes($clockOut);
                $totalWorkedMinutes += $duration;
                $lastClockOut = $clockOut;
            } else {
                $activeSession = $log;
                // If log is currently active (user is clocked in right now)
                if ($clockIn) {
                    $refTime = ($date === now()->toDateString()) ? now() : $clockIn;
                    $duration = (int) $clockIn->diffInMinutes($refTime);
                    $totalWorkedMinutes += $duration;
                }
            }
        }

        $hours = floor($totalWorkedMinutes / 60);
        $minutes = $totalWorkedMinutes % 60;
        $formattedTotalHours = "{$hours}h {$minutes}m";

        $breakHours = floor($totalBreaksMinutes / 60);
        $breakMins = $totalBreaksMinutes % 60;
        $formattedBreakHours = "{$breakHours}h {$breakMins}m";

        return [
            'attendance' => $attendance,
            'tracking' => $tracking,
            'logs' => $logs,
            'sessionCount' => $logs->count(),
            'totalWorkedMinutes' => $totalWorkedMinutes,
            'formattedTotalHours' => $formattedTotalHours,
            'totalBreaksMinutes' => $totalBreaksMinutes,
            'formattedBreakHours' => $formattedBreakHours,
            'hasActiveSession' => ! is_null($activeSession),
            'firstClockIn' => $attendance?->clock_in_time ?: $logs->first()?->clock_in_time,
            'lastClockOut' => $attendance?->clock_out_time ?: $logs->whereNotNull('clock_out_time')->last()?->clock_out_time,
        ];
    }

    public function getSelectedUserDetailProperty(): ?array
    {
        if (! $this->selectedUserId) {
            return null;
        }

        $user = User::with('department')->find($this->selectedUserId);
        if (! $user) {
            return null;
        }

        $date = $this->selectedDate ?: now()->toDateString();
        $stats = $this->getUserStats($user, $date);

        return array_merge(['user' => $user, 'date' => $date], $stats);
    }

    public function render()
    {
        $date = $this->selectedDate ?: now()->toDateString();

        $query = User::where('role', 'employee')
            ->with(['department', 'attendances' => function ($q) use ($date) {
                $q->whereDate('attendance_date', $date)->with(['logs' => function ($lq) {
                    $lq->orderBy('clock_in_time', 'asc');
                }]);
            }, 'slotTrackings' => function ($q) use ($date) {
                $q->whereDate('tracking_date', $date);
            }])
            ->orderBy('name', 'asc');

        if ($this->departmentFilter !== '') {
            $query->where('department_id', $this->departmentFilter);
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $employees = $query->paginate(12);

        // Pre-calculate stats map for current page employees
        $employeeStats = [];
        $totalClockedInCount = 0;
        $totalFlaggedCount = 0;
        $totalWorkMinutesOverall = 0;

        foreach ($employees as $employee) {
            $stats = $this->getUserStats($employee, $date);
            $employeeStats[$employee->id] = $stats;

            if ($stats['firstClockIn']) {
                $totalClockedInCount++;
            }

            if ($stats['tracking'] && ($stats['tracking']->slot2_is_flagged || $stats['tracking']->lunch_exceeded || $stats['tracking']->slot3_is_flagged)) {
                $totalFlaggedCount++;
            }

            $totalWorkMinutesOverall += $stats['totalWorkedMinutes'];
        }

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $totalStaff = User::where('role', 'employee')->count();
        $avgHours = $totalClockedInCount > 0 ? round(($totalWorkMinutesOverall / $totalClockedInCount) / 60, 1) : 0;

        return view('admin.attendance-logs.attendance-logs', [
            'employees' => $employees,
            'employeeStats' => $employeeStats,
            'departments' => $departments,
            'totalStaff' => $totalStaff,
            'totalClockedInCount' => $totalClockedInCount,
            'totalFlaggedCount' => $totalFlaggedCount,
            'avgHours' => $avgHours,
            'selectedUserDetail' => $this->selectedUserDetail,
        ]);
    }
};
