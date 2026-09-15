<?php

use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Leave Request Approval Management - TechMage')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $departmentFilter = '';

    public bool $showReviewModal = false;

    public bool $showCancelModal = false;

    public ?LeaveRequest $selectedLeaveRequest = null;

    public ?LeaveRequest $cancelLeaveTarget = null;

    public bool $confirmCancelCheck = false;

    public string $decision = 'approved';

    public string $leave_category = 'casual';

    public string $admin_remarks = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function openReviewModal(int $id): void
    {
        $this->resetValidation();
        $this->selectedLeaveRequest = LeaveRequest::with('user.department')->findOrFail($id);
        $this->decision = $this->selectedLeaveRequest->status === 'rejected' ? 'rejected' : 'approved';
        $this->leave_category = $this->selectedLeaveRequest->leave_category ?: ($this->selectedLeaveRequest->request_type === 'half_day' ? 'half_day' : 'casual');
        $this->admin_remarks = $this->selectedLeaveRequest->admin_remarks ?: '';
        $this->showReviewModal = true;
    }

    public function closeReviewModal(): void
    {
        $this->selectedLeaveRequest = null;
        $this->showReviewModal = false;
    }

    public function saveReview(): void
    {
        if (! $this->selectedLeaveRequest) {
            return;
        }

        $rules = [
            'decision' => 'required|in:approved,rejected',
            'admin_remarks' => 'nullable|string|max:1000',
        ];

        if ($this->decision === 'approved') {
            $rules['leave_category'] = 'required|in:paid,unpaid,sick,casual,special,holiday,half_day';
        }

        $this->validate($rules);

        $leaveRequest = $this->selectedLeaveRequest;
        $paymentType = ($this->decision === 'approved' && $this->leave_category === 'unpaid') ? 'unpaid' : 'paid';

        $leaveRequest->update([
            'status' => $this->decision,
            'leave_category' => $this->decision === 'approved' ? $this->leave_category : null,
            'payment_type' => $this->decision === 'approved' ? $paymentType : null,
            'admin_remarks' => $this->admin_remarks,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($this->decision === 'approved') {
            $currentDate = Carbon::parse($leaveRequest->start_date);
            $endDate = Carbon::parse($leaveRequest->end_date);

            $attendanceStatus = 'on_leave';
            if ($this->leave_category === 'holiday') {
                $attendanceStatus = 'holiday';
            } elseif ($this->leave_category === 'half_day') {
                $attendanceStatus = 'half_day';
            }

            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->toDateString();

                Attendance::updateOrCreate(
                    [
                        'user_id' => $leaveRequest->user_id,
                        'attendance_date' => $dateStr,
                    ],
                    [
                        'status' => $attendanceStatus,
                        'leave_request_id' => $leaveRequest->id,
                        'leave_category' => $this->leave_category,
                        'leave_payment_type' => $paymentType,
                    ]
                );

                $currentDate->addDay();
            }
        } elseif ($this->decision === 'rejected') {
            // Revert attendance records if previously approved
            $linkedAttendances = Attendance::with('logs')
                ->where('leave_request_id', $leaveRequest->id)
                ->get();

            foreach ($linkedAttendances as $att) {
                if ($att->logs->isNotEmpty()) {
                    $att->update([
                        'status' => 'present',
                        'leave_request_id' => null,
                        'leave_category' => null,
                        'leave_payment_type' => null,
                    ]);
                } else {
                    $att->delete();
                }
            }
        }

        $this->showReviewModal = false;
        $this->selectedLeaveRequest = null;

        $this->dispatch('toast-show', [
            'message' => 'Leave request decision saved successfully.',
            'type' => 'success',
        ]);
    }

    public function quickReject(int $id): void
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'rejected',
            'leave_category' => null,
            'payment_type' => null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Revert attendance records if previously approved
        $linkedAttendances = Attendance::with('logs')
            ->where('leave_request_id', $leaveRequest->id)
            ->get();

        foreach ($linkedAttendances as $att) {
            if ($att->logs->isNotEmpty()) {
                $att->update([
                    'status' => 'present',
                    'leave_request_id' => null,
                    'leave_category' => null,
                    'leave_payment_type' => null,
                ]);
            } else {
                $att->delete();
            }
        }

        $this->dispatch('toast-show', [
            'message' => 'Leave request rejected successfully.',
            'type' => 'info',
        ]);
    }

    public function openCancelModal(int $id): void
    {
        $this->cancelLeaveTarget = LeaveRequest::with('user')->findOrFail($id);
        $this->confirmCancelCheck = false;
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->cancelLeaveTarget = null;
        $this->confirmCancelCheck = false;
        $this->showCancelModal = false;
    }

    public function confirmCancelLeave(): void
    {
        if (! $this->cancelLeaveTarget || ! $this->confirmCancelCheck) {
            return;
        }

        $leaveRequest = LeaveRequest::findOrFail($this->cancelLeaveTarget->id);

        if ($leaveRequest->status === 'approved') {
            $linkedAttendances = Attendance::with('logs')
                ->where('leave_request_id', $leaveRequest->id)
                ->get();

            foreach ($linkedAttendances as $att) {
                if ($att->logs->isNotEmpty()) {
                    $att->update([
                        'status' => 'present',
                        'leave_request_id' => null,
                        'leave_category' => null,
                        'leave_payment_type' => null,
                    ]);
                } else {
                    $att->delete();
                }
            }
        }

        $leaveRequest->delete();

        $this->closeCancelModal();
        $this->resetPage();

        $this->dispatch('toast-show', [
            'message' => 'Leave request cancelled successfully.',
            'type' => 'info',
        ]);
    }

    public function render()
    {
        $query = LeaveRequest::with(['user.department', 'reviewer'])
            ->orderBy('id', 'desc');

        if ($this->search !== '') {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->departmentFilter !== '') {
            $query->whereHas('user', function ($q) {
                $q->where('department_id', $this->departmentFilter);
            });
        }

        $leaveRequests = $query->paginate(10);

        $totalPending = LeaveRequest::where('status', 'pending')->count();
        $totalApprovedThisMonth = LeaveRequest::where('status', 'approved')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->count();
        $totalPaidLeaves = LeaveRequest::where('status', 'approved')->where('payment_type', 'paid')->count();
        $totalUnpaidLeaves = LeaveRequest::where('status', 'approved')->where('payment_type', 'unpaid')->count();

        $departments = Department::orderBy('name')->get();

        return view('admin.leave-requests.leave-requests', [
            'leaveRequests' => $leaveRequests,
            'totalPending' => $totalPending,
            'totalApprovedThisMonth' => $totalApprovedThisMonth,
            'totalPaidLeaves' => $totalPaidLeaves,
            'totalUnpaidLeaves' => $totalUnpaidLeaves,
            'departments' => $departments,
        ]);
    }
};
