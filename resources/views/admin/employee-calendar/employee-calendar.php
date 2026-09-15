<?php

use App\Models\Attendance;
use App\Models\DailySlotTracking;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Employee Attendance & Leave Calendar - TechMage')] class extends Component
{
    public User $user;

    public int $currentYear;

    public int $currentMonth;

    public string $viewMode = 'calendar'; // 'calendar' or 'list'

    public ?string $selectedDateForModal = null;

    public bool $showDetailModal = false;

    public function mount(User $user): void
    {
        $this->user = $user->load('department');
        $this->currentYear = (int) now()->format('Y');
        $this->currentMonth = (int) now()->format('m');
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = (int) $date->format('Y');
        $this->currentMonth = (int) $date->format('m');
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = (int) $date->format('Y');
        $this->currentMonth = (int) $date->format('m');
    }

    public function goToToday(): void
    {
        $this->currentYear = (int) now()->format('Y');
        $this->currentMonth = (int) now()->format('m');
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function openDateModal(string $dateString): void
    {
        $this->selectedDateForModal = $dateString;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedDateForModal = null;
        $this->showDetailModal = false;
    }

    public function getSelectedDateDetailProperty(): ?array
    {
        if (! $this->selectedDateForModal) {
            return null;
        }

        $targetDate = $this->selectedDateForModal;

        $tracking = DailySlotTracking::where('user_id', $this->user->id)
            ->whereDate('tracking_date', $targetDate)
            ->first();

        $attendance = Attendance::with(['logs' => function ($q) {
            $q->orderBy('clock_in_time', 'asc');
        }])
            ->where('user_id', $this->user->id)
            ->whereDate('attendance_date', $targetDate)
            ->first();

        $leaveRequest = LeaveRequest::where('user_id', $this->user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $targetDate)
            ->whereDate('end_date', '>=', $targetDate)
            ->first();

        $logs = $attendance ? $attendance->logs : collect();

        $totalWorkedMinutes = 0;
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
            } elseif ($clockIn) {
                $refTime = ($targetDate === now()->toDateString()) ? now() : $clockIn;
                $totalWorkedMinutes += (int) $clockIn->diffInMinutes($refTime);
            }
        }

        $hours = floor($totalWorkedMinutes / 60);
        $minutes = $totalWorkedMinutes % 60;

        return [
            'targetDate' => $targetDate,
            'tracking' => $tracking,
            'attendance' => $attendance,
            'leaveRequest' => $leaveRequest,
            'logs' => $logs,
            'formattedTotalHours' => "{$hours}h {$minutes}m",
            'totalBreaksMinutes' => $totalBreaksMinutes,
            'sessionCount' => $logs->count(),
        ];
    }

    public function render()
    {
        $currentMonthCarbon = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $gridStart = $currentMonthCarbon->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $currentMonthCarbon->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $attendancesInMonth = Attendance::with(['logs' => function ($q) {
            $q->orderBy('clock_in_time', 'asc');
        }])
            ->where('user_id', $this->user->id)
            ->whereBetween('attendance_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get()
            ->keyBy(fn ($item) => $item->attendance_date->toDateString());

        $trackingsInMonth = DailySlotTracking::where('user_id', $this->user->id)
            ->whereBetween('tracking_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get()
            ->keyBy(fn ($item) => $item->tracking_date->toDateString());

        $leaveRequestsInMonth = LeaveRequest::where('user_id', $this->user->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($gridStart, $gridEnd) {
                $q->whereBetween('start_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
                    ->orWhereBetween('end_date', [$gridStart->toDateString(), $gridEnd->toDateString()]);
            })
            ->get();

        $calendarDays = [];
        $cursor = $gridStart->copy();

        $presentDaysCount = 0;
        $leaveDaysCount = 0;
        $halfDayDaysCount = 0;
        $absentDaysCount = 0;

        while ($cursor->lte($gridEnd)) {
            $dateStr = $cursor->toDateString();
            $att = $attendancesInMonth->get($dateStr);
            $tr = $trackingsInMonth->get($dateStr);

            // Find matching approved leave request if any
            $matchingLeave = $leaveRequestsInMonth->first(function ($l) use ($dateStr) {
                return $l->start_date->toDateString() <= $dateStr && $l->end_date->toDateString() >= $dateStr;
            });

            $isCurrentMonth = ($cursor->month === $this->currentMonth);
            $isToday = ($dateStr === now()->toDateString());
            $isOffDay = $this->user->isOffDay($cursor);

            $workedMins = 0;
            if ($att && $att->logs->isNotEmpty()) {
                foreach ($att->logs as $log) {
                    if ($log->clock_out_time) {
                        $workedMins += $log->duration_minutes ?: (int) $log->clock_in_time->diffInMinutes($log->clock_out_time);
                    } elseif ($log->clock_in_time) {
                        $ref = $isToday ? now() : $log->clock_in_time;
                        $workedMins += (int) $log->clock_in_time->diffInMinutes($ref);
                    }
                }
            }

            $formattedHours = $workedMins > 0 ? (floor($workedMins / 60).'h '.($workedMins % 60).'m') : null;

            $status = 'none';
            if ($att) {
                $status = $att->status ?: 'present';
            } elseif ($tr && $tr->slot1_checkin_time) {
                $status = 'present';
            } elseif ($isCurrentMonth && $cursor->isPast() && ! $isOffDay && ! $isToday) {
                $status = 'absent';
            } elseif ($isOffDay) {
                $status = 'weekend';
            }

            if ($isCurrentMonth) {
                if ($status === 'present') {
                    $presentDaysCount++;
                } elseif ($status === 'on_leave' || $status === 'leave') {
                    $leaveDaysCount++;
                } elseif ($status === 'half_day') {
                    $halfDayDaysCount++;
                } elseif ($status === 'absent') {
                    $absentDaysCount++;
                }
            }

            $calendarDays[] = [
                'date' => $dateStr,
                'dayNumber' => $cursor->day,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'isWeekend' => $isOffDay,
                'attendance' => $att,
                'tracking' => $tr,
                'leaveRequest' => $matchingLeave,
                'status' => $status,
                'formattedHours' => $formattedHours,
            ];

            $cursor->addDay();
        }

        // List view query for monthly logs
        $monthlyLogs = Attendance::with(['logs' => function ($q) {
            $q->orderBy('clock_in_time', 'asc');
        }])
            ->where('user_id', $this->user->id)
            ->whereMonth('attendance_date', $this->currentMonth)
            ->whereYear('attendance_date', $this->currentYear)
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('admin.employee-calendar.employee-calendar', [
            'calendarDays' => $calendarDays,
            'currentMonthLabel' => $currentMonthCarbon->format('F Y'),
            'presentDaysCount' => $presentDaysCount,
            'leaveDaysCount' => $leaveDaysCount,
            'halfDayDaysCount' => $halfDayDaysCount,
            'absentDaysCount' => $absentDaysCount,
            'monthlyLogs' => $monthlyLogs,
            'selectedDateDetail' => $this->selectedDateDetail,
        ]);
    }
};
