<?php

use App\Models\Attendance;
use App\Models\DailySlotTracking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.employee')] #[Title('My Working Hours & Slots - TechMage')] class extends Component
{
    use WithPagination;

    public string $dateFilter = '';

    public ?int $selectedTrackingId = null;

    public bool $showDetailModal = false;

    #[On('slot-updated')]
    public function refreshComponent(): void
    {
        // Re-render when employee updates status
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
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedTrackingId = null;
        $this->showDetailModal = false;
    }

    public function getSelectedTrackingDetailProperty(): ?array
    {
        if (! $this->selectedTrackingId) {
            return null;
        }

        $user = Auth::user();
        if (! $user) {
            return null;
        }

        $tracking = DailySlotTracking::where('user_id', $user->id)
            ->where('id', $this->selectedTrackingId)
            ->first();

        if (! $tracking) {
            return null;
        }

        $attendance = Attendance::with(['logs' => function ($q) {
            $q->orderBy('clock_in_time', 'asc');
        }])
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', $tracking->tracking_date->toDateString())
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
                $refTime = ($tracking->tracking_date->toDateString() === now()->toDateString()) ? now() : $clockIn;
                $totalWorkedMinutes += (int) $clockIn->diffInMinutes($refTime);
            }
        }

        $hours = floor($totalWorkedMinutes / 60);
        $minutes = $totalWorkedMinutes % 60;

        return [
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

        $query = DailySlotTracking::where('user_id', $user->id)
            ->orderBy('tracking_date', 'desc');

        if ($this->dateFilter !== '') {
            $query->whereDate('tracking_date', $this->dateFilter);
        }

        $trackings = $query->paginate(10);

        // Pre-calculate stats map for employee's daily trackings
        $trackingStats = [];
        foreach ($trackings as $tr) {
            $attendance = Attendance::with(['logs' => function ($q) {
                $q->orderBy('clock_in_time', 'asc');
            }])
                ->where('user_id', $user->id)
                ->whereDate('attendance_date', $tr->tracking_date->toDateString())
                ->first();

            $logs = $attendance ? $attendance->logs : collect();
            $totalWorked = 0;
            foreach ($logs as $log) {
                if ($log->clock_out_time) {
                    $totalWorked += $log->duration_minutes ?: (int) $log->clock_in_time->diffInMinutes($log->clock_out_time);
                } elseif ($log->clock_in_time) {
                    $ref = ($tr->tracking_date->toDateString() === now()->toDateString()) ? now() : $log->clock_in_time;
                    $totalWorked += (int) $log->clock_in_time->diffInMinutes($ref);
                }
            }

            $h = floor($totalWorked / 60);
            $m = $totalWorked % 60;

            $trackingStats[$tr->id] = [
                'formattedHours' => "{$h}h {$m}m",
                'attendance' => $attendance,
                'sessionCount' => $logs->count(),
            ];
        }

        return view('employee.attendance-logs.attendance-logs', [
            'trackings' => $trackings,
            'trackingStats' => $trackingStats,
            'selectedTrackingDetail' => $this->selectedTrackingDetail,
        ]);
    }
};
