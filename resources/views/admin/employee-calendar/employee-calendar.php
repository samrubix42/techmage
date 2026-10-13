<?php

use App\Models\Attendance;
use App\Models\DailySlotTracking;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    #[On('slot-updated')]
    public function refreshCalendar(): void
    {
        // Re-render component on live slot/attendance updates
    }

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

        $totalWorkedMinutes = DailySlotTracking::calculateWorkedMinutesForDay($tracking, $attendance);
        $hours = floor($totalWorkedMinutes / 60);
        $minutes = $totalWorkedMinutes % 60;
        $isUnderEightHours = DailySlotTracking::isUnderEightHours($totalWorkedMinutes);

        $holiday = Holiday::whereDate('holiday_date', $targetDate)->first();

        return [
            'targetDate' => $targetDate,
            'tracking' => $tracking,
            'attendance' => $attendance,
            'leaveRequest' => $leaveRequest,
            'holiday' => $holiday,
            'logs' => $logs,
            'workedMinutes' => $totalWorkedMinutes,
            'formattedTotalHours' => "{$hours}h {$minutes}m",
            'isUnderEightHours' => $isUnderEightHours,
            'totalBreaksMinutes' => DailySlotTracking::calculateBreaksMinutesForDay($tracking, $attendance),
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

        $holidaysInMonth = Holiday::whereBetween('holiday_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get()
            ->keyBy(fn ($item) => $item->holiday_date->toDateString());

        $calendarDays = [];
        $cursor = $gridStart->copy();

        $presentDaysCount = 0;
        $leaveDaysCount = 0;
        $halfDayDaysCount = 0;
        $absentDaysCount = 0;
        $holidayDaysCount = 0;

        while ($cursor->lte($gridEnd)) {
            $dateStr = $cursor->toDateString();
            $att = $attendancesInMonth->get($dateStr);
            $tr = $trackingsInMonth->get($dateStr);
            $holiday = $holidaysInMonth->get($dateStr);

            // Find matching approved leave request if any
            $matchingLeave = $leaveRequestsInMonth->first(function ($l) use ($dateStr) {
                return $l->start_date->toDateString() <= $dateStr && $l->end_date->toDateString() >= $dateStr;
            });

            $isCurrentMonth = ($cursor->month === $this->currentMonth);
            $isToday = ($dateStr === now()->toDateString());
            $isOffDay = $this->user->isOffDay($cursor);

            $workedMins = DailySlotTracking::calculateWorkedMinutesForDay($tr, $att);
            $formattedHours = $workedMins > 0 ? (floor($workedMins / 60).'h '.($workedMins % 60).'m') : null;
            $isUnderEightHours = DailySlotTracking::isUnderEightHours($workedMins);

            $hasClockIn = ($att && ($att->clock_in_time || $att->logs->isNotEmpty())) || ($tr && $tr->slot1_checkin_time);

            $status = 'none';
            if ($holiday) {
                if ($hasClockIn) {
                    $status = 'holiday_worked';
                } else {
                    $status = 'holiday';
                }
            } elseif ($hasClockIn) {
                $status = ($att && in_array($att->status, ['on_leave', 'leave', 'half_day'])) ? $att->status : 'present';
            } elseif ($att && $att->status) {
                $status = $att->status;
            } elseif ($isCurrentMonth && $cursor->isPast() && ! $isOffDay && ! $isToday) {
                $status = 'absent';
            } elseif ($isOffDay) {
                $status = 'weekend';
            }

            if ($isCurrentMonth) {
                if ($status === 'present' || $status === 'holiday_worked') {
                    $presentDaysCount++;
                } elseif ($status === 'on_leave' || $status === 'leave') {
                    $leaveDaysCount++;
                } elseif ($status === 'half_day') {
                    $halfDayDaysCount++;
                } elseif ($status === 'absent') {
                    $absentDaysCount++;
                }

                if ($holiday) {
                    $holidayDaysCount++;
                }
            }

            $calendarDays[] = [
                'date' => $dateStr,
                'dayNumber' => $cursor->day,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'isWeekend' => $isOffDay,
                'isHoliday' => (bool) $holiday,
                'holiday' => $holiday,
                'attendance' => $att,
                'tracking' => $tr,
                'leaveRequest' => $matchingLeave,
                'status' => $status,
                'workedMinutes' => $workedMins,
                'formattedHours' => $formattedHours,
                'isUnderEightHours' => $isUnderEightHours,
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
            'holidayDaysCount' => $holidayDaysCount,
            'monthlyLogs' => $monthlyLogs,
            'trackingsInMonth' => $trackingsInMonth,
            'selectedDateDetail' => $this->selectedDateDetail,
        ]);
    }
};
