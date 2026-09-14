<?php

use App\Models\DailyTaskReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.employee')] #[Title('Daily Task Reports - TechMage')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $dateFilter = '';

    public ?int $selectedReportId = null;

    public bool $showDetailModal = false;

    public ?int $confirmingDeleteId = null;

    public function getSelectedReportProperty(): ?DailyTaskReport
    {
        if (! $this->selectedReportId) {
            return null;
        }

        return DailyTaskReport::where('user_id', Auth::id())->find($this->selectedReportId);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function viewReport(int $id): void
    {
        $report = DailyTaskReport::where('user_id', Auth::id())->find($id);

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

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteReport(int $id): void
    {
        $report = DailyTaskReport::where('user_id', Auth::id())->find($id);

        if ($report) {
            $report->delete();
            $this->dispatch('toast-show', [
                'message' => 'Daily task report deleted successfully.',
                'type' => 'success',
                'position' => 'top-right',
            ]);
        }

        $this->confirmingDeleteId = null;

        if ($this->selectedReportId === $id) {
            $this->closeDetailModal();
        }
    }

    public function render()
    {
        $query = DailyTaskReport::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->dateFilter !== '') {
            $query->whereDate('date', $this->dateFilter);
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('title_description', 'like', '%'.$search.'%');
            });
        }

        $reports = $query->paginate(10);

        return view('employee.daily-task-report-list.daily-task-report-list', [
            'reports' => $reports,
        ]);
    }
};
