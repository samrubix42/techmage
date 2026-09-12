<?php

use App\Models\DailySlotTracking;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Admin Dashboard - TechMage')] class extends Component
{
    public string $search = '';

    public string $statusFilter = 'all'; // 'all', 'flagged', 'slot2_flagged', 'lunch_exceeded', 'slot3_flagged'

    public function render()
    {
        $today = now()->toDateString();

        $query = DailySlotTracking::with(['user.department'])
            ->whereDate('tracking_date', $today);

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter === 'flagged') {
            $query->where(function ($q) {
                $q->where('slot2_is_flagged', true)
                    ->orWhere('lunch_exceeded', true)
                    ->orWhere('slot3_is_flagged', true);
            });
        } elseif ($this->statusFilter === 'slot2_flagged') {
            $query->where('slot2_is_flagged', true);
        } elseif ($this->statusFilter === 'lunch_exceeded') {
            $query->where('lunch_exceeded', true);
        } elseif ($this->statusFilter === 'slot3_flagged') {
            $query->where('slot3_is_flagged', true);
        }

        $trackings = $query->latest()->get();

        $totalEmployees = User::where('role', 'employee')->count();
        $clockedInToday = DailySlotTracking::whereDate('tracking_date', $today)->whereNotNull('slot1_checkin_time')->count();
        $lunchExceededCount = DailySlotTracking::whereDate('tracking_date', $today)->where('lunch_exceeded', true)->count();
        $totalRedFlagsCount = DailySlotTracking::whereDate('tracking_date', $today)
            ->where(function ($q) {
                $q->where('slot2_is_flagged', true)
                    ->orWhere('lunch_exceeded', true)
                    ->orWhere('slot3_is_flagged', true);
            })->count();

        return view('admin.dashboard.dashboard', [
            'trackings' => $trackings,
            'totalEmployees' => $totalEmployees,
            'clockedInToday' => $clockedInToday,
            'lunchExceededCount' => $lunchExceededCount,
            'totalRedFlagsCount' => $totalRedFlagsCount,
        ]);
    }
};
