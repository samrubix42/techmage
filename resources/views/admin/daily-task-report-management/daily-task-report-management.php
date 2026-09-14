<?php

use App\Models\DailyTaskReport;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Employee Daily Reports - TechMage')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $employeeFilter = '';

    public string $departmentFilter = '';

    public string $statusFilter = 'all';

    public string $dateFilter = '';

    public ?int $selectedReportId = null;

    public bool $showDetailModal = false;

    public function getSelectedReportProperty(): ?DailyTaskReport
    {
        if (! $this->selectedReportId) {
            return null;
        }

        return DailyTaskReport::with(['user.department', 'checkedBy'])->find($this->selectedReportId);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEmployeeFilter(): void
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

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->employeeFilter = '';
        $this->departmentFilter = '';
        $this->statusFilter = 'all';
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function viewReport(int $id): void
    {
        $report = DailyTaskReport::with(['user.department', 'checkedBy'])->find($id);

        if ($report) {
            $this->selectedReportId = $report->id;
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal(): void
    {
        $this->selectedReportId = null;
        $this->showDetailModal = false;
    }

    public function markAsChecked(int $id): void
    {
        $report = DailyTaskReport::find($id);

        if ($report) {
            $report->update([
                'is_checked' => true,
                'checked_at' => now(),
                'checked_by' => Auth::id(),
            ]);

            $this->dispatch('toast-show', [
                'message' => 'Daily report marked as reviewed & done.',
                'type' => 'success',
                'position' => 'top-right',
            ]);
        }
    }

    public function unmarkAsChecked(int $id): void
    {
        $report = DailyTaskReport::find($id);

        if ($report) {
            $report->update([
                'is_checked' => false,
                'checked_at' => null,
                'checked_by' => null,
            ]);

            $this->dispatch('toast-show', [
                'message' => 'Daily report marked as pending review.',
                'type' => 'info',
                'position' => 'top-right',
            ]);
        }
    }

    public function render()
    {
        $query = DailyTaskReport::with(['user.department', 'checkedBy'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->employeeFilter !== '') {
            $query->where('user_id', $this->employeeFilter);
        }

        if ($this->departmentFilter !== '') {
            $query->whereHas('user', function ($q) {
                $q->where('department_id', $this->departmentFilter);
            });
        }

        if ($this->statusFilter === 'pending') {
            $query->where('is_checked', false);
        } elseif ($this->statusFilter === 'checked') {
            $query->where('is_checked', true);
        }

        if ($this->dateFilter !== '') {
            $query->whereDate('date', $this->dateFilter);
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('title_description', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        $reports = $query->paginate(10);
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.daily-task-report-management.daily-task-report-management', [
            'reports' => $reports,
            'employees' => $employees,
            'departments' => $departments,
        ]);
    }
};
