<?php

use App\Models\Attendance;
use App\Models\DailySlotTracking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.employee')] #[Title('My Working Hours & Calendar - TechMage')] class extends Component
{
    use WithPagination;

    public string $viewMode = 'calendar'; // 'calendar' or 'list'

    public int $currentYear;

    public int $currentMonth;

    public string $dateFilter = '';

    public ?int $selectedTrackingId = null;

    public ?string $selectedDateForModal = null;

    public bool $showDetailModal = false;

    #[On('slot-updated')]
    public function refreshComponent(): void
    {
        // Re-render when employee updates status
    }

    public function mount(): void
    {
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

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function openDetailModal(int $trackingId): void
    {
        $this->selectedTrackingId = $trackingId;
        $this->selectedDateForModal = null;
        $this->showDetailModal = true;
    }

    public function openDateModal(string $dateString): void
    {
        $this->selectedDateForModal = $dateString;
        $this->selectedTrackingId = null;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedTrackingId = null;
        $this->selectedDateForModal = null;
        $this->showDetailModal = false;
    }

    public function getSelectedTrackingDetailProperty(): ?array
    {
        $user = Auth::user();
        if (! $user || (! $this->selectedTrackingId && ! $this->selectedDateForModal)) {
            return null;
        }

        $tracking = null;
        $targetDate = null;

        if ($this->selectedTrackingId) {
            $tracking = DailySlotTracking::where('user_id', $user->id)
                ->where('id', $this->selectedTrackingId)
                ->first();
            $targetDate = $tracking?->tracking_date?->toDateString();
        } else {
            $targetDate = $this->selectedDateForModal;
            $tracking = DailySlotTracking::where('user_id', $user->id)
                ->whereDate('tracking_date', $targetDate)
                ->first();
        }

        if (! $targetDate) {
            return null;
        }

        $attendance = Attendance::with(['logs' => function ($q) {
            $q->orderBy('clock_in_time', 'asc');
        }])
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', $targetDate)
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
            'logs' => $logs,
            'formattedTotalHours' => "{$hours}h {$minutes}m",
            'totalBreaksMinutes' => $totalBreaksMinutes,
            'sessionCount' => $logs->count(),
        ];
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Calendar Grid Data Generation
        $currentMonthCarbon = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $gridStart = $currentMonthCarbon->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $currentMonthCarbon->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $attendancesInMonth = Attendance::with(['logs' => function ($q) {
            $q->orderBy('clock_in_time', 'asc');
        }])
            ->where('user_id', $user->id)
            ->whereBetween('attendance_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                return $item->attendance_date->toDateString();
            });

        $trackingsInMonth = DailySlotTracking::where('user_id', $user->id)
            ->whereBetween('tracking_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                return $item->tracking_date->toDateString();
            });

        $calendarDays = [];
        $cursor = $gridStart->copy();

        $presentDaysCount = 0;
        $leaveDaysCount = 0;
        $absentDaysCount = 0;

        while ($cursor->lte($gridEnd)) {
            $dateStr = $cursor->toDateString();
            $att = $attendancesInMonth->get($dateStr);
            $tr = $trackingsInMonth->get($dateStr);

            $isCurrentMonth = ($cursor->month === $this->currentMonth);
            $isToday = ($dateStr === now()->toDateString());
            $isOffDay = $user->isOffDay($cursor);

            // Calculate worked hours for calendar day
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

            // Determine status badge
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
                'status' => $status,
                'formattedHours' => $formattedHours,
            ];

            $cursor->addDay();
        }

        // 2. List View Query
        $query = DailySlotTracking::where('user_id', $user->id)
            ->orderBy('tracking_date', 'desc');

        if ($this->dateFilter !== '') {
            $query->whereDate('tracking_date', $this->dateFilter);
        }

        $trackings = $query->paginate(10);

        $trackingStats = [];
        foreach ($trackings as $trItem) {
            $attItem = Attendance::with(['logs' => function ($q) {
                $q->orderBy('clock_in_time', 'asc');
            }])
                ->where('user_id', $user->id)
                ->whereDate('attendance_date', $trItem->tracking_date->toDateString())
                ->first();

            $logs = $attItem ? $attItem->logs : collect();
            $totalWorked = 0;
            foreach ($logs as $log) {
                if ($log->clock_out_time) {
                    $totalWorked += $log->duration_minutes ?: (int) $log->clock_in_time->diffInMinutes($log->clock_out_time);
                } elseif ($log->clock_in_time) {
                    $ref = ($trItem->tracking_date->toDateString() === now()->toDateString()) ? now() : $log->clock_in_time;
                    $totalWorked += (int) $log->clock_in_time->diffInMinutes($ref);
                }
            }

            $h = floor($totalWorked / 60);
            $m = $totalWorked % 60;

            $trackingStats[$trItem->id] = [
                'formattedHours' => "{$h}h {$m}m",
                'attendance' => $attItem,
                'sessionCount' => $logs->count(),
            ];
        }

        return view('employee.attendance-logs.attendance-logs', [
            'calendarDays' => $calendarDays,
            'currentMonthLabel' => $currentMonthCarbon->format('F Y'),
            'presentDaysCount' => $presentDaysCount,
            'leaveDaysCount' => $leaveDaysCount,
            'absentDaysCount' => $absentDaysCount,
            'trackings' => $trackings,
            'trackingStats' => $trackingStats,
            'selectedTrackingDetail' => $this->selectedTrackingDetail,
        ]);
    }
};
