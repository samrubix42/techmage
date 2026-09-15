<?php

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.employee')] #[Title('My Leave Requests - TechMage')] class extends Component
{
    use WithPagination;

    public string $request_type = 'single_day';

    public string $start_date = '';

    public string $end_date = '';

    public string $reason = '';

    public string $statusFilter = '';

    public bool $showApplyModal = false;

    public bool $showDetailModal = false;

    public ?LeaveRequest $selectedLeaveRequest = null;

    public function mount(): void
    {
        $this->start_date = now()->toDateString();
        $this->end_date = now()->toDateString();
    }

    public function updatedRequestType(string $value): void
    {
        if (in_array($value, ['single_day', 'half_day'])) {
            $this->end_date = $this->start_date;
        }
    }

    public function updatedStartDate(string $value): void
    {
        if (in_array($this->request_type, ['single_day', 'half_day']) || empty($this->end_date) || $this->end_date < $value) {
            $this->end_date = $value;
        }
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openApplyModal(): void
    {
        $this->resetValidation();
        $this->request_type = 'single_day';
        $this->start_date = now()->toDateString();
        $this->end_date = now()->toDateString();
        $this->reason = '';
        $this->showApplyModal = true;
    }

    public function closeApplyModal(): void
    {
        $this->showApplyModal = false;
    }

    public function submitLeaveRequest(): void
    {
        if (in_array($this->request_type, ['single_day', 'half_day'])) {
            $this->end_date = $this->start_date;
        }

        $this->validate([
            'request_type' => 'required|in:single_day,half_day,multiple_days',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:5|max:1000',
        ]);

        if ($this->request_type === 'half_day') {
            $totalDays = 0.5;
        } else {
            $start = Carbon::parse($this->start_date);
            $end = Carbon::parse($this->end_date);
            $totalDays = (float) ($start->diffInDays($end) + 1);
        }

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'request_type' => $this->request_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_days' => $totalDays,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        $this->showApplyModal = false;
        $this->reset(['reason']);
        $this->resetPage();

        $this->dispatch('toast-show', [
            'message' => 'Leave request submitted successfully!',
            'type' => 'success',
        ]);
    }

    public function openDetailModal(int $id): void
    {
        $this->selectedLeaveRequest = LeaveRequest::with(['reviewer'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedLeaveRequest = null;
        $this->showDetailModal = false;
    }

    public function render()
    {
        $userId = Auth::id();

        $query = LeaveRequest::where('user_id', $userId)
            ->orderBy('id', 'desc');

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $leaveRequests = $query->paginate(10);

        $totalSubmitted = LeaveRequest::where('user_id', $userId)->count();
        $totalPending = LeaveRequest::where('user_id', $userId)->where('status', 'pending')->count();
        $totalApproved = LeaveRequest::where('user_id', $userId)->where('status', 'approved')->count();
        $totalRejected = LeaveRequest::where('user_id', $userId)->where('status', 'rejected')->count();

        return view('employee.leave-requests.leave-requests', [
            'leaveRequests' => $leaveRequests,
            'totalSubmitted' => $totalSubmitted,
            'totalPending' => $totalPending,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
        ]);
    }
};
