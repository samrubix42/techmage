<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <i class="ri-calendar-todo-line text-amber-600"></i>
                        Leave Approval & Categorization System
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Employee Leave Requests</h1>
                <p class="text-slate-600 mt-1 text-sm">Review employee single and multi-day leave applications, select leave type (Paid, Unpaid, Sick, Casual, Special, Holiday, Half Day), approve/cancel requests, and sync attendance logs.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-slate-50 border border-slate-200 rounded-md px-4 py-2 text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Pending Action</span>
                    <span class="text-lg font-bold text-amber-600">{{ $totalPending }} Requests</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Pending Approvals -->
        <div class="bg-white border {{ $totalPending > 0 ? 'border-amber-300 bg-amber-50/10' : 'border-slate-200/80' }} rounded-md p-5 hover:border-amber-400 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-800">Pending Review</span>
                <div class="w-8 h-8 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <i class="ri-time-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-700">{{ $totalPending }}</div>
            <p class="text-xs text-amber-700 mt-1 font-medium">Awaiting Admin Decision</p>
        </div>

        <!-- Metric 2: Approved This Month -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Approved This Month</span>
                <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="ri-checkbox-circle-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ $totalApprovedThisMonth }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">{{ now()->format('F Y') }}</p>
        </div>

        <!-- Metric 3: Total Paid Leaves -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Paid Leaves</span>
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i class="ri-money-dollar-circle-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalPaidLeaves }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Total Approved Paid</p>
        </div>

        <!-- Metric 4: Total Unpaid Leaves -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Unpaid Leaves</span>
                <div class="w-8 h-8 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <i class="ri-calendar-event-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalUnpaidLeaves }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Total Approved Unpaid</p>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-4">
        <!-- Filter Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 rounded-md bg-slate-50 border border-slate-200">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search employee name or email..." 
                        class="w-full pl-9 pr-3 py-1.5 text-xs rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white"
                    />
                    <i class="ri-search-line text-slate-400 absolute left-3 top-2 text-sm"></i>
                </div>

                <!-- Status Filter -->
                <select 
                    wire:model.live="statusFilter" 
                    class="py-1.5 px-3 text-xs rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white font-medium text-slate-700 shadow-xs"
                >
                    <option value="">All Statuses</option>
                    <option value="pending">Pending Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>

                <!-- Department Filter -->
                <select 
                    wire:model.live="departmentFilter" 
                    class="py-1.5 px-3 text-xs rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white font-medium text-slate-700 shadow-xs"
                >
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($search || $statusFilter || $departmentFilter)
                <button 
                    wire:click="$set('search', ''); $set('statusFilter', ''); $set('departmentFilter', '');" 
                    class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors"
                >
                    <i class="ri-refresh-line"></i> Reset Filters
                </button>
            @endif
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border border-slate-200/80 rounded-md">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="uppercase bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Employee</th>
                        <th class="px-4 py-3">Leave Duration</th>
                        <th class="px-4 py-3">Requested Dates</th>
                        <th class="px-4 py-3">Reason</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Selected Leave Type</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leaveRequests as $req)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $req->status === 'pending' ? 'bg-amber-50/20' : '' }}">
                            <!-- Employee Info -->
                            <td class="px-4 py-3.5 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($req->user->name ?? 'E', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $req->user->name ?? 'Unknown' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $req->user->email ?? '' }}</div>
                                        <div class="mt-0.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $req->user->department->name ?? 'General' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Leave Duration Type -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($req->request_type === 'single_day')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="ri-calendar-event-line"></i> Single Day
                                    </span>
                                @elseif($req->request_type === 'half_day')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        <i class="ri-time-line"></i> Half Day
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="ri-calendar-2-line"></i> Multiple Days
                                    </span>
                                @endif
                                <div class="mt-1 font-bold text-slate-600 text-[11px]">
                                    {{ $req->total_days == 0.5 ? '0.5 Day' : $req->total_days.' '.Str::plural('Day', $req->total_days) }}
                                </div>
                            </td>

                            <!-- Requested Dates -->
                            <td class="px-4 py-3.5 align-middle font-bold text-slate-800">
                                @if($req->request_type === 'single_day')
                                    {{ $req->start_date->format('M d, Y') }}
                                @else
                                    {{ $req->start_date->format('M d, Y') }} <i class="ri-arrow-right-line text-slate-400 mx-0.5"></i> {{ $req->end_date->format('M d, Y') }}
                                @endif
                            </td>

                            <!-- Reason -->
                            <td class="px-4 py-3.5 align-middle max-w-xs truncate text-slate-600">
                                {{ Str::limit($req->reason, 45) }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <i class="ri-time-line text-amber-600"></i> Pending Review
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ri-checkbox-circle-line text-emerald-600"></i> Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                        <i class="ri-close-circle-line text-red-600"></i> Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Category / Type -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($req->status === 'approved')
                                    @php
                                        $typeLabels = [
                                            'paid' => 'Paid Leave',
                                            'unpaid' => 'Unpaid Leave',
                                            'sick' => 'Sick Leave',
                                            'casual' => 'Casual Leave',
                                            'special' => 'Special Leave',
                                            'holiday' => 'Holiday',
                                            'half_day' => 'Half Day Leave',
                                        ];
                                        $label = $typeLabels[$req->leave_category] ?? ucfirst($req->leave_category ?? 'Leave');
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $label }}
                                    </span>
                                @elseif($req->status === 'rejected')
                                    <span class="text-red-500 text-[11px] font-medium italic">Rejected</span>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Not Set</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 align-middle text-right space-x-1">
                                <button 
                                    wire:click="openReviewModal({{ $req->id }})" 
                                    class="px-3 py-1.5 rounded-md text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs inline-flex items-center gap-1.5 cursor-pointer"
                                >
                                    <i class="ri-edit-box-line text-slate-500"></i>
                                    <span>{{ $req->status === 'pending' ? 'Review & Approve' : 'Edit Decision' }}</span>
                                </button>

                                <button 
                                    wire:click="openCancelModal({{ $req->id }})" 
                                    class="px-2.5 py-1.5 rounded-md text-xs font-bold text-red-700 bg-white hover:bg-red-50 border border-red-200 transition-colors shadow-2xs inline-flex items-center gap-1 cursor-pointer"
                                    title="Cancel Leave Request"
                                >
                                    <i class="ri-delete-bin-line text-red-500"></i>
                                    <span>Cancel Leave</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <i class="ri-calendar-check-line text-4xl text-slate-300 block"></i>
                                    <p class="font-bold text-slate-700">No leave requests found.</p>
                                    <p class="text-xs text-slate-400">Try adjusting your search query or filters above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $leaveRequests->links() }}
        </div>
    </div>

    <!-- Admin Review Modal -->
    @if($showReviewModal && $selectedLeaveRequest)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md overflow-y-auto"
            x-data
            @keydown.escape.window="$wire.closeReviewModal()"
        >
            <div class="bg-white rounded-md border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 space-y-5 text-left relative transform transition-all">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-md bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center font-bold text-sm shrink-0">
                            {{ strtoupper(substr($selectedLeaveRequest->user->name ?? 'E', 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $selectedLeaveRequest->user->name }}</h2>
                            <p class="text-xs text-slate-400">{{ $selectedLeaveRequest->user->email }} • <span class="font-semibold text-slate-600">{{ $selectedLeaveRequest->user->department->name ?? 'General' }}</span></p>
                        </div>
                    </div>
                    <button 
                        wire:click="closeReviewModal" 
                        class="w-8 h-8 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <!-- Leave Summary Card -->
                <div class="bg-slate-50 border border-slate-200 rounded-md p-3.5 space-y-2 text-xs">
                    <div class="flex items-center justify-between font-bold">
                        <span class="text-slate-800 flex items-center gap-1.5">
                            <i class="{{ $selectedLeaveRequest->request_type === 'single_day' ? 'ri-calendar-event-line' : 'ri-calendar-2-line' }} text-amber-600"></i>
                            {{ $selectedLeaveRequest->request_type === 'single_day' ? 'Single Day Request' : 'Multiple Days Request' }}
                        </span>
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-extrabold text-[11px]">
                            {{ $selectedLeaveRequest->total_days }} {{ Str::plural('Day', $selectedLeaveRequest->total_days) }}
                        </span>
                    </div>

                    <div class="text-slate-700 font-semibold">
                        Dates: 
                        @if($selectedLeaveRequest->request_type === 'single_day')
                            {{ $selectedLeaveRequest->start_date->format('l, F j, Y') }}
                        @else
                            {{ $selectedLeaveRequest->start_date->format('M j, Y') }} to {{ $selectedLeaveRequest->end_date->format('M j, Y') }}
                        @endif
                    </div>

                    <div class="pt-1 text-slate-600 border-t border-slate-200/60">
                        <span class="font-bold text-slate-700 block mb-0.5">Employee Reason:</span>
                        <p class="italic bg-white p-2 rounded border border-slate-200 text-slate-800">{{ $selectedLeaveRequest->reason }}</p>
                    </div>
                </div>

                <!-- Form Fields -->
                <form wire:submit="saveReview" class="space-y-4 text-xs">
                    <!-- Decision Radio (Approve / Reject) -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wide mb-2 text-[11px]">Admin Decision</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 p-3 rounded-md border {{ $decision === 'approved' ? 'border-emerald-500 bg-emerald-50/60 text-emerald-900 font-bold' : 'border-slate-200 bg-white text-slate-700' }} cursor-pointer transition-colors">
                                <input 
                                    type="radio" 
                                    wire:model.live="decision" 
                                    value="approved" 
                                    class="text-emerald-600 focus:ring-emerald-500"
                                />
                                <div class="flex items-center gap-1.5">
                                    <i class="ri-checkbox-circle-fill text-emerald-600 text-base"></i>
                                    <span>Approve Leave</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-md border {{ $decision === 'rejected' ? 'border-red-500 bg-red-50/60 text-red-900 font-bold' : 'border-slate-200 bg-white text-slate-700' }} cursor-pointer transition-colors">
                                <input 
                                    type="radio" 
                                    wire:model.live="decision" 
                                    value="rejected" 
                                    class="text-red-600 focus:ring-red-500"
                                />
                                <div class="flex items-center gap-1.5">
                                    <i class="ri-close-circle-fill text-red-600 text-base"></i>
                                    <span>Reject Leave</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    @if($decision === 'approved')
                        <!-- Single Select Dropdown for Leave Type -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Select Leave Type</label>
                            <select 
                                wire:model="leave_category" 
                                class="w-full px-3 py-2 text-xs font-semibold rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white shadow-xs"
                            >
                                <option value="paid">Paid Leave</option>
                                <option value="unpaid">Unpaid Leave</option>
                                <option value="sick">Sick Leave</option>
                                <option value="casual">Casual Leave</option>
                                <option value="special">Special Leave</option>
                                <option value="holiday">Holiday</option>
                                <option value="half_day">Half Day Leave</option>
                            </select>
                            @error('leave_category') <span class="text-red-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Admin Remarks -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Admin Remarks / Feedback (Optional)</label>
                        <textarea 
                            wire:model="admin_remarks" 
                            rows="2" 
                            placeholder="Add notes for the employee regarding this decision..." 
                            class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white"
                        ></textarea>
                        @error('admin_remarks') <span class="text-red-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="closeReviewModal" 
                            class="px-4 py-2 text-xs font-bold rounded-md border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-xs font-bold rounded-md text-white {{ $decision === 'approved' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700' }} transition-colors cursor-pointer shadow-xs"
                        >
                            Save Decision & Sync Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Admin Leave Cancellation Confirmation Modal -->
    @if($showCancelModal && $cancelLeaveTarget)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md overflow-y-auto"
            x-data
            @keydown.escape.window="$wire.closeCancelModal()"
        >
            <div class="bg-white rounded-md border border-slate-200/80 shadow-2xl max-w-md w-full p-6 space-y-5 text-left relative transform transition-all">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-md bg-red-50 text-red-600 border border-red-200/60 flex items-center justify-center font-bold text-lg shrink-0">
                            <i class="ri-delete-bin-line"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Cancel Leave Request (Admin)</h2>
                            <p class="text-xs text-slate-400">Cancel leave application for {{ $cancelLeaveTarget->user->name ?? 'Employee' }}.</p>
                        </div>
                    </div>
                    <button 
                        wire:click="closeCancelModal" 
                        class="w-8 h-8 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Leave Target Summary Card -->
                    <div class="p-3.5 rounded-md bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between font-bold">
                            <span class="text-slate-800">
                                {{ $cancelLeaveTarget->request_type === 'single_day' ? 'Single Day Leave' : 'Multiple Days Leave' }}
                            </span>
                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 text-[10px] font-bold">
                                {{ $cancelLeaveTarget->total_days }} {{ Str::plural('Day', $cancelLeaveTarget->total_days) }}
                            </span>
                        </div>
                        <div class="font-bold text-slate-900 text-xs">
                            @if($cancelLeaveTarget->request_type === 'single_day')
                                {{ $cancelLeaveTarget->start_date->format('l, F j, Y') }}
                            @else
                                {{ $cancelLeaveTarget->start_date->format('M j, Y') }} to {{ $cancelLeaveTarget->end_date->format('M j, Y') }}
                            @endif
                        </div>
                        <p class="text-slate-600 italic">"{{ $cancelLeaveTarget->reason }}"</p>
                    </div>

                    @if($cancelLeaveTarget->status === 'approved')
                        <div class="p-3 rounded-md bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2">
                            <i class="ri-information-line text-amber-600 text-base shrink-0 mt-0.5"></i>
                            <div>
                                <strong>Admin Notice:</strong> Cancelling this leave request will remove the leave status from {{ $cancelLeaveTarget->user->name }}'s attendance records.
                            </div>
                        </div>
                    @endif

                    <!-- Checkbox Confirmation -->
                    <div class="p-3 rounded-md border border-slate-200 bg-white">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input 
                                type="checkbox" 
                                wire:model.live="confirmCancelCheck" 
                                class="mt-0.5 text-red-600 focus:ring-red-500 rounded border-slate-300"
                            />
                            <span class="text-xs font-bold text-slate-800 leading-snug">
                                I confirm as Admin that I want to cancel this leave request.
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Action Check Button Footer -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        wire:click="closeCancelModal" 
                        class="px-4 py-2 text-xs font-bold rounded-md border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                    >
                        Back
                    </button>
                    <button 
                        type="button" 
                        wire:click="confirmCancelLeave" 
                        @if(!$confirmCancelCheck) disabled @endif
                        class="px-4 py-2 text-xs font-bold rounded-md text-white bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all inline-flex items-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <i class="ri-checkbox-circle-line text-sm"></i>
                        <span>Confirm & Cancel Leave</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
