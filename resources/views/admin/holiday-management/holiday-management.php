<?php

use App\Models\Attendance;
use App\Models\Holiday;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Holiday Management - TechMage')] class extends Component
{
    use WithPagination;

    public string $viewMode = 'calendar'; // 'calendar' or 'list'

    public int $calendarYear;

    public int $calendarMonth;

    public string $search = '';

    public string $yearFilter = '';

    public string $monthFilter = '';

    public string $typeFilter = '';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showViewModal = false;

    public bool $showDeleteModal = false;

    public ?int $holidayId = null;

    public string $name = '';

    public string $holiday_date = '';

    public string $date_mode = 'single'; // 'single', 'range', 'multiple'

    public string $start_date = '';

    public string $end_date = '';

    public bool $range_skip_weekends = false;

    /** @var array<int, string> */
    public array $multiple_dates = [];

    public string $new_multiple_date = '';

    public string $type = 'public';

    public string $description = '';

    public ?Holiday $selectedHoliday = null;

    /** @var Collection<int, array> */
    public Collection $workedEmployees;

    public function mount(): void
    {
        $this->yearFilter = (string) now()->format('Y');
        $this->calendarYear = (int) now()->format('Y');
        $this->calendarMonth = (int) now()->format('m');
        $this->workedEmployees = collect();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['calendar', 'list']) ? $mode : 'calendar';
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarYear = (int) $date->format('Y');
        $this->calendarMonth = (int) $date->format('m');
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarYear = (int) $date->format('Y');
        $this->calendarMonth = (int) $date->format('m');
    }

    public function goToToday(): void
    {
        $this->calendarYear = (int) now()->format('Y');
        $this->calendarMonth = (int) now()->format('m');
    }

    public function openCreateModalForDate(string $date): void
    {
        if (Carbon::parse($date)->lt(today())) {
            session()->flash('error', 'Cannot add holidays for past dates.');

            return;
        }

        $this->resetForm();
        $this->holiday_date = $date;
        $this->start_date = $date;
        $this->end_date = $date;
        $this->date_mode = 'single';
        $this->showCreateModal = true;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingYearFilter(): void
    {
        $this->resetPage();
    }

    public function updatingMonthFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->holidayId = null;
        $this->name = '';
        $this->holiday_date = '';
        $this->date_mode = 'single';
        $this->start_date = '';
        $this->end_date = '';
        $this->range_skip_weekends = false;
        $this->multiple_dates = [];
        $this->new_multiple_date = '';
        $this->type = 'public';
        $this->description = '';
        $this->resetErrorBag();
    }

    public function addMultipleDate(): void
    {
        if (empty($this->new_multiple_date)) {
            return;
        }

        $dateStr = Carbon::parse($this->new_multiple_date)->toDateString();
        if (Carbon::parse($dateStr)->lt(today())) {
            $this->addError('new_multiple_date', 'Holiday date cannot be in the past.');

            return;
        }

        if (! in_array($dateStr, $this->multiple_dates)) {
            $this->multiple_dates[] = $dateStr;
            sort($this->multiple_dates);
        }

        $this->new_multiple_date = '';
        $this->resetErrorBag('multiple_dates');
        $this->resetErrorBag('new_multiple_date');
    }

    public function removeMultipleDate(string $date): void
    {
        $this->multiple_dates = array_values(array_filter($this->multiple_dates, fn ($d) => $d !== $date));
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showDeleteModal = false;
        $this->selectedHoliday = null;
        $this->workedEmployees = collect();
        $this->resetForm();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->holiday_date = now()->toDateString();
        $this->start_date = now()->toDateString();
        $this->end_date = now()->addDays(1)->toDateString();
        $this->showCreateModal = true;
    }

    public function createHoliday(): void
    {
        $baseRules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:public,company,optional'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];

        if ($this->date_mode === 'range') {
            $validated = $this->validate(array_merge($baseRules, [
                'start_date' => ['required', 'date', 'after_or_equal:today'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            ]), [
                'start_date.after_or_equal' => 'Holiday start date cannot be in the past.',
                'end_date.after_or_equal' => 'Holiday end date must be on or after the start date.',
            ]);

            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);
            $targetDates = [];
            $cursor = $start->copy();

            while ($cursor->lte($end)) {
                if (! ($this->range_skip_weekends && $cursor->isWeekend())) {
                    $targetDates[] = $cursor->toDateString();
                }
                $cursor->addDay();
            }

            if (empty($targetDates)) {
                $this->addError('end_date', 'No valid dates selected in the date range.');

                return;
            }

            $existingDates = Holiday::whereIn('holiday_date', $targetDates)
                ->pluck('holiday_date')
                ->map(fn ($d) => $d->toDateString())
                ->toArray();

            if (count($existingDates) === count($targetDates)) {
                $this->addError('start_date', 'All dates in the selected range already have holidays.');

                return;
            }

            $createdCount = 0;
            foreach ($targetDates as $dateStr) {
                if (! in_array($dateStr, $existingDates)) {
                    Holiday::create([
                        'name' => trim($validated['name']),
                        'holiday_date' => $dateStr,
                        'type' => $validated['type'],
                        'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
                    ]);
                    $createdCount++;
                }
            }

            $skippedCount = count($existingDates);
            $msg = "Created {$createdCount} holidays for \"{$validated['name']}\" successfully.";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} duplicate dates were skipped).";
            }

            session()->flash('message', $msg);
            $this->closeModals();

            return;
        }

        if ($this->date_mode === 'multiple') {
            if (! empty($this->new_multiple_date)) {
                $dateStr = Carbon::parse($this->new_multiple_date)->toDateString();
                if (Carbon::parse($dateStr)->lt(today())) {
                    $this->addError('new_multiple_date', 'Holiday date cannot be in the past.');

                    return;
                }
                if (! in_array($dateStr, $this->multiple_dates)) {
                    $this->multiple_dates[] = $dateStr;
                    sort($this->multiple_dates);
                }
                $this->new_multiple_date = '';
            }

            $validated = $this->validate($baseRules);

            if (empty($this->multiple_dates)) {
                $this->addError('multiple_dates', 'Please add at least one holiday date.');

                return;
            }

            foreach ($this->multiple_dates as $dateStr) {
                if (Carbon::parse($dateStr)->lt(today())) {
                    $this->addError('multiple_dates', 'Holiday dates cannot be in the past.');

                    return;
                }
            }

            $existingDates = Holiday::whereIn('holiday_date', $this->multiple_dates)
                ->pluck('holiday_date')
                ->map(fn ($d) => $d->toDateString())
                ->toArray();

            if (count($existingDates) === count($this->multiple_dates)) {
                $this->addError('multiple_dates', 'All selected dates already have holidays.');

                return;
            }

            $createdCount = 0;
            foreach ($this->multiple_dates as $dateStr) {
                if (! in_array($dateStr, $existingDates)) {
                    Holiday::create([
                        'name' => trim($validated['name']),
                        'holiday_date' => $dateStr,
                        'type' => $validated['type'],
                        'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
                    ]);
                    $createdCount++;
                }
            }

            $skippedCount = count($existingDates);
            $msg = "Created {$createdCount} holidays for \"{$validated['name']}\" successfully.";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} duplicate dates were skipped).";
            }

            session()->flash('message', $msg);
            $this->closeModals();

            return;
        }

        // Single date mode (default)
        $validated = $this->validate(array_merge($baseRules, [
            'holiday_date' => ['required', 'date', 'after_or_equal:today'],
        ]), [
            'holiday_date.after_or_equal' => 'Holiday date cannot be in the past.',
        ]);

        $existing = Holiday::whereDate('holiday_date', $validated['holiday_date'])->exists();
        if ($existing) {
            $this->addError('holiday_date', 'A holiday already exists on this date.');

            return;
        }

        Holiday::create([
            'name' => trim($validated['name']),
            'holiday_date' => $validated['holiday_date'],
            'type' => $validated['type'],
            'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
        ]);

        session()->flash('message', 'Holiday "'.$validated['name'].'" created successfully.');
        $this->closeModals();
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $holiday = Holiday::findOrFail($id);

        $this->holidayId = $holiday->id;
        $this->name = (string) $holiday->name;
        $this->holiday_date = $holiday->holiday_date->toDateString();
        $this->type = (string) $holiday->type;
        $this->description = (string) ($holiday->description ?? '');

        $this->showEditModal = true;
    }

    public function updateHoliday(): void
    {
        $holiday = Holiday::findOrFail($this->holidayId);
        $dateChanged = $holiday->holiday_date->toDateString() !== Carbon::parse($this->holiday_date)->toDateString();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'holiday_date' => ['required', 'date'],
            'type' => ['required', 'in:public,company,optional'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];

        if ($dateChanged) {
            $rules['holiday_date'][] = 'after_or_equal:today';
        }

        $validated = $this->validate($rules, [
            'holiday_date.after_or_equal' => 'Holiday date cannot be in the past.',
        ]);

        $existing = Holiday::whereDate('holiday_date', $validated['holiday_date'])
            ->where('id', '!=', $this->holidayId)
            ->exists();
        if ($existing) {
            $this->addError('holiday_date', 'A holiday already exists on this date.');

            return;
        }

        $holiday->update([
            'name' => trim($validated['name']),
            'holiday_date' => $validated['holiday_date'],
            'type' => $validated['type'],
            'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
        ]);

        session()->flash('message', 'Holiday "'.$validated['name'].'" updated successfully.');
        $this->closeModals();
    }

    public function openViewModal(int $id): void
    {
        $this->selectedHoliday = Holiday::findOrFail($id);
        $dateStr = $this->selectedHoliday->holiday_date->toDateString();

        // Find employees who clocked in / worked on this holiday
        $attendances = Attendance::with(['user.department', 'logs'])
            ->whereDate('attendance_date', $dateStr)
            ->where(function ($q) {
                $q->whereNotNull('clock_in_time')
                    ->orWhereHas('logs');
            })
            ->get();

        $workedList = [];
        foreach ($attendances as $att) {
            $user = $att->user;
            if (! $user) {
                continue;
            }

            $totalWorkedMinutes = 0;
            foreach ($att->logs as $log) {
                if ($log->clock_out_time) {
                    $totalWorkedMinutes += $log->duration_minutes ?: (int) $log->clock_in_time->diffInMinutes($log->clock_out_time);
                } elseif ($log->clock_in_time) {
                    $totalWorkedMinutes += (int) $log->clock_in_time->diffInMinutes(now());
                }
            }

            $hours = floor($totalWorkedMinutes / 60);
            $minutes = $totalWorkedMinutes % 60;

            $workedList[] = [
                'user' => $user,
                'clock_in_time' => $att->clock_in_time ?: $att->logs->first()?->clock_in_time,
                'clock_out_time' => $att->clock_out_time ?: $att->logs->whereNotNull('clock_out_time')->last()?->clock_out_time,
                'total_hours' => "{$hours}h {$minutes}m",
                'logs_count' => $att->logs->count(),
                'status' => $att->status,
            ];
        }

        $this->workedEmployees = collect($workedList);
        $this->showViewModal = true;
    }

    public function openDeleteModal(int $id): void
    {
        $this->selectedHoliday = Holiday::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function deleteHoliday(): void
    {
        if (! $this->selectedHoliday) {
            return;
        }

        $name = $this->selectedHoliday->name;
        $this->selectedHoliday->delete();

        session()->flash('message', 'Holiday "'.$name.'" deleted successfully.');
        $this->closeModals();
    }

    public function render()
    {
        $query = Holiday::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->yearFilter)) {
            $query->whereYear('holiday_date', (int) $this->yearFilter);
        }

        if (! empty($this->monthFilter)) {
            $query->whereMonth('holiday_date', (int) $this->monthFilter);
        }

        if (! empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        $holidays = $query->orderBy('holiday_date', 'asc')->paginate(10);

        // Fetch counts of employees who worked on each holiday on this page
        $holidayDates = $holidays->pluck('holiday_date')->map(fn ($d) => $d->toDateString())->toArray();
        $workedCountsByDate = [];
        if (! empty($holidayDates)) {
            $workedCountsByDate = Attendance::whereIn('attendance_date', $holidayDates)
                ->where(function ($q) {
                    $q->whereNotNull('clock_in_time')
                        ->orWhereHas('logs');
                })
                ->selectRaw('attendance_date, count(distinct user_id) as worker_count')
                ->groupBy('attendance_date')
                ->pluck('worker_count', 'attendance_date')
                ->toArray();
        }

        // Overview stats for the selected year
        $activeYear = ! empty($this->yearFilter) ? (int) $this->yearFilter : (int) now()->format('Y');
        $totalHolidaysCount = Holiday::whereYear('holiday_date', $activeYear)->count();
        $upcomingHolidaysCount = Holiday::whereDate('holiday_date', '>=', now()->toDateString())->count();
        $pastHolidaysCount = Holiday::whereDate('holiday_date', '<', now()->toDateString())->count();

        $workedOnHolidaysThisYearCount = Attendance::whereYear('attendance_date', $activeYear)
            ->whereIn('attendance_date', function ($sub) use ($activeYear) {
                $sub->select('holiday_date')
                    ->from('holidays')
                    ->whereYear('holiday_date', $activeYear);
            })
            ->where(function ($q) {
                $q->whereNotNull('clock_in_time')
                    ->orWhereHas('logs');
            })
            ->distinct('user_id')
            ->count('user_id');

        // Calendar grid calculations
        $currentMonthCarbon = Carbon::create($this->calendarYear, $this->calendarMonth, 1);
        $gridStart = $currentMonthCarbon->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $currentMonthCarbon->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $holidaysInGrid = Holiday::whereBetween('holiday_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get()
            ->keyBy(fn ($item) => $item->holiday_date->toDateString());

        $gridHolidayDates = $holidaysInGrid->pluck('holiday_date')->map(fn ($d) => $d->toDateString())->toArray();
        $gridWorkedCounts = [];
        if (! empty($gridHolidayDates)) {
            $gridWorkedCounts = Attendance::whereIn('attendance_date', $gridHolidayDates)
                ->where(function ($q) {
                    $q->whereNotNull('clock_in_time')
                        ->orWhereHas('logs');
                })
                ->selectRaw('attendance_date, count(distinct user_id) as worker_count')
                ->groupBy('attendance_date')
                ->pluck('worker_count', 'attendance_date')
                ->toArray();
        }

        $calendarDays = [];
        $cursor = $gridStart->copy();
        $monthHolidaysCount = 0;

        while ($cursor->lte($gridEnd)) {
            $dateStr = $cursor->toDateString();
            $holiday = $holidaysInGrid->get($dateStr);
            $isCurrentMonth = ($cursor->month === $this->calendarMonth);
            $isToday = ($dateStr === now()->toDateString());
            $workerCount = $holiday ? ($gridWorkedCounts[$dateStr] ?? 0) : 0;

            if ($isCurrentMonth && $holiday) {
                $monthHolidaysCount++;
            }

            $calendarDays[] = [
                'date' => $dateStr,
                'dayNumber' => $cursor->day,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'isPast' => $cursor->lt(today()),
                'isWeekend' => $cursor->isWeekend(),
                'holiday' => $holiday,
                'workerCount' => $workerCount,
            ];

            $cursor->addDay();
        }

        // Yearly holidays for the selected calendar year
        $yearlyHolidays = Holiday::whereYear('holiday_date', $this->calendarYear)
            ->orderBy('holiday_date', 'asc')
            ->get();

        $yearlyHolidayDates = $yearlyHolidays->pluck('holiday_date')->map(fn ($d) => $d->toDateString())->toArray();
        $yearlyWorkedCounts = [];
        if (! empty($yearlyHolidayDates)) {
            $yearlyWorkedCounts = Attendance::whereIn('attendance_date', $yearlyHolidayDates)
                ->where(function ($q) {
                    $q->whereNotNull('clock_in_time')
                        ->orWhereHas('logs');
                })
                ->selectRaw('attendance_date, count(distinct user_id) as worker_count')
                ->groupBy('attendance_date')
                ->pluck('worker_count', 'attendance_date')
                ->toArray();
        }

        return view('admin.holiday-management.holiday-management', [
            'holidays' => $holidays,
            'workedCountsByDate' => $workedCountsByDate,
            'calendarDays' => $calendarDays,
            'currentMonthLabel' => $currentMonthCarbon->format('F Y'),
            'currentMonthCarbon' => $currentMonthCarbon,
            'monthHolidaysCount' => $monthHolidaysCount,
            'yearlyHolidays' => $yearlyHolidays,
            'yearlyWorkedCounts' => $yearlyWorkedCounts,
            'totalHolidaysCount' => $totalHolidaysCount,
            'upcomingHolidaysCount' => $upcomingHolidaysCount,
            'pastHolidaysCount' => $pastHolidaysCount,
            'workedOnHolidaysThisYearCount' => $workedOnHolidaysThisYearCount,
            'activeYear' => $activeYear,
        ]);
    }
};
