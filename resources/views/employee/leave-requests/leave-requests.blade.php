<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <i class="ri-calendar-check-line text-amber-600"></i>
                        Leave Request Portal
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">My Leave Requests</h1>
                <p class="text-slate-600 mt-1 text-sm">Submit leave applications for single day, half day, or multiple days and track admin approval status.</p>
            </div>

            <div class="flex items-center gap-3">
                <button 
                    wire:click="openApplyModal" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 transition-colors shadow-xs inline-flex items-center gap-2 cursor-pointer"
                >
                    <i class="ri-add-line text-base"></i>
                    <span>Apply for Leave</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Applications -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Applied</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i class="ri-file-list-3-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalSubmitted }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">All Time Applications</p>
        </div>

        <!-- Metric 2: Pending Approval -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Pending</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i class="ri-time-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalPending }}</div>
            <p class="text-xs text-amber-600 mt-1 font-medium">Awaiting Admin Action</p>
        </div>

        <!-- Metric 3: Approved -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Approved</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="ri-checkbox-circle-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ $totalApproved }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Granted Leaves</p>
        </div>

        <!-- Metric 4: Rejected -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">Rejected</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold">
                    <i class="ri-close-circle-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-red-600">{{ $totalRejected }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Declined Applications</p>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
        <!-- Filter Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Filter Status:</span>
                <select 
                    wire:model.live="statusFilter" 
                    class="py-1.5 px-3 text-xs rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white font-medium text-slate-700 shadow-xs"
                >
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            @if($statusFilter !== '')
                <button 
                    wire:click="$set('statusFilter', '')" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors"
                >
                    <i class="ri-refresh-line"></i> Clear Filter
                </button>
            @endif
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="uppercase bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Leave Type</th>
                        <th class="px-4 py-3">Date Range</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Reason</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Admin Decision</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leaveRequests as $req)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Leave Type -->
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
                            </td>

                            <!-- Date Range -->
                            <td class="px-4 py-3.5 align-middle font-bold text-slate-800">
                                @if(in_array($req->request_type, ['single_day', 'half_day']))
                                    {{ $req->start_date->format('M d, Y') }}
                                @else
                                    {{ $req->start_date->format('M d, Y') }} <i class="ri-arrow-right-line text-slate-400 mx-0.5"></i> {{ $req->end_date->format('M d, Y') }}
                                @endif
                            </td>

                            <!-- Duration -->
                            <td class="px-4 py-3.5 align-middle">
                                <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-700 border border-slate-200 text-[11px]">
                                    {{ $req->total_days == 0.5 ? '0.5 Day' : $req->total_days.' '.Str::plural('Day', $req->total_days) }}
                                </span>
                            </td>

                            <!-- Reason -->
                            <td class="px-4 py-3.5 align-middle max-w-xs truncate text-slate-600">
                                {{ Str::limit($req->reason, 40) }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ri-time-line text-amber-500"></i> Pending
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ri-checkbox-circle-line text-emerald-600"></i> Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                        <i class="ri-close-circle-line text-red-600"></i> Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Admin Decision -->
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
                                    <span class="text-red-500 text-[11px] font-medium italic">Declined</span>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Under Review</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 align-middle text-right">
                                <button 
                                    wire:click="openDetailModal({{ $req->id }})" 
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs inline-flex items-center gap-1 cursor-pointer"
                                >
                                    <i class="ri-eye-line text-slate-500"></i> View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <i class="ri-calendar-event-line text-4xl text-slate-300 block"></i>
                                    <p class="font-bold text-slate-700">No leave requests found.</p>
                                    <p class="text-xs text-slate-400">Click "Apply for Leave" above to submit your first leave application.</p>
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

    <!-- Apply Leave Request Modal -->
    @if($showApplyModal)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md overflow-y-auto"
            x-data
            @keydown.escape.window="$wire.closeApplyModal()"
        >
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 space-y-5 text-left relative transform transition-all">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center font-bold text-lg shrink-0">
                            <i class="ri-calendar-event-line"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Apply for Leave</h2>
                            <p class="text-xs text-slate-400">Select leave duration and provide details for admin approval.</p>
                        </div>
                    </div>
                    <button 
                        wire:click="closeApplyModal" 
                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="submitLeaveRequest" class="space-y-4">
                    <!-- Request Type Selector (Single Day vs Half Day vs Multiple Days) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Leave Duration Type</label>
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Single Day -->
                            <label class="flex flex-col items-center justify-center p-3 rounded-xl border text-center {{ $request_type === 'single_day' ? 'border-amber-500 bg-amber-50/50 text-amber-900 font-bold' : 'border-slate-200 bg-white text-slate-700' }} cursor-pointer transition-colors">
                                <input 
                                    type="radio" 
                                    wire:model.live="request_type" 
                                    value="single_day" 
                                    class="sr-only"
                                />
                                <i class="ri-calendar-event-line text-base mb-0.5 text-amber-600"></i>
                                <span class="text-xs font-bold">Single Day</span>
                                <span class="text-[10px] text-slate-500 font-normal">1 Day</span>
                            </label>

                            <!-- Half Day -->
                            <label class="flex flex-col items-center justify-center p-3 rounded-xl border text-center {{ $request_type === 'half_day' ? 'border-sky-500 bg-sky-50/50 text-sky-900 font-bold' : 'border-slate-200 bg-white text-slate-700' }} cursor-pointer transition-colors">
                                <input 
                                    type="radio" 
                                    wire:model.live="request_type" 
                                    value="half_day" 
                                    class="sr-only"
                                />
                                <i class="ri-time-line text-base mb-0.5 text-sky-600"></i>
                                <span class="text-xs font-bold">Half Day</span>
                                <span class="text-[10px] text-slate-500 font-normal">0.5 Day</span>
                            </label>

                            <!-- Multiple Days -->
                            <label class="flex flex-col items-center justify-center p-3 rounded-xl border text-center {{ $request_type === 'multiple_days' ? 'border-amber-500 bg-amber-50/50 text-amber-900 font-bold' : 'border-slate-200 bg-white text-slate-700' }} cursor-pointer transition-colors">
                                <input 
                                    type="radio" 
                                    wire:model.live="request_type" 
                                    value="multiple_days" 
                                    class="sr-only"
                                />
                                <i class="ri-calendar-2-line text-base mb-0.5 text-amber-600"></i>
                                <span class="text-xs font-bold">Multiple Days</span>
                                <span class="text-[10px] text-slate-500 font-normal">Range</span>
                            </label>
                        </div>
                    </div>

                    <!-- Date Selection Fields -->
                    @if(in_array($request_type, ['single_day', 'half_day']))
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Select Leave Date</label>
                            <input 
                                type="date" 
                                wire:model.live="start_date" 
                                class="w-full px-3 py-2 text-xs font-medium rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white"
                            />
                            @error('start_date') <span class="text-red-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">From Date (Start)</label>
                                <input 
                                    type="date" 
                                    wire:model.live="start_date" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white"
                                />
                                @error('start_date') <span class="text-red-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">To Date (End)</label>
                                <input 
                                    type="date" 
                                    wire:model.live="end_date" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white"
                                />
                                @error('end_date') <span class="text-red-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Reason -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Reason for Leave</label>
                        <textarea 
                            wire:model="reason" 
                            rows="3" 
                            placeholder="Please explain the reason for your leave request..." 
                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white"
                        ></textarea>
                        @error('reason') <span class="text-red-500 text-[11px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="closeApplyModal" 
                            class="px-4 py-2 text-xs font-bold rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-xs font-bold rounded-lg text-white bg-amber-600 hover:bg-amber-700 transition-colors cursor-pointer shadow-xs"
                        >
                            Submit Leave Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Inspect Details Modal -->
    @if($showDetailModal && $selectedLeaveRequest)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md overflow-y-auto"
            x-data
            @keydown.escape.window="$wire.closeDetailModal()"
        >
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xl max-w-md w-full p-6 space-y-5 text-left relative transform transition-all">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Leave Request Details</h2>
                    <button 
                        wire:click="closeDetailModal" 
                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Status Badge Banner -->
                    <div class="p-3 rounded-xl border flex items-center justify-between {{ $selectedLeaveRequest->status === 'approved' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($selectedLeaveRequest->status === 'rejected' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-amber-50 border-amber-200 text-amber-800') }}">
                        <div class="font-bold flex items-center gap-2">
                            <i class="{{ $selectedLeaveRequest->status === 'approved' ? 'ri-checkbox-circle-fill text-emerald-600' : ($selectedLeaveRequest->status === 'rejected' ? 'ri-close-circle-fill text-red-600' : 'ri-time-fill text-amber-600') }} text-lg"></i>
                            <span class="uppercase tracking-wider font-extrabold text-[11px]">Status: {{ $selectedLeaveRequest->status }}</span>
                        </div>
                        <span class="font-semibold text-[11px]">{{ $selectedLeaveRequest->total_days == 0.5 ? '0.5 Day' : $selectedLeaveRequest->total_days.' '.Str::plural('Day', $selectedLeaveRequest->total_days) }}</span>
                    </div>

                    <!-- Dates Info -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Date Requested</span>
                        <div class="font-bold text-slate-900 text-sm">
                            @if(in_array($selectedLeaveRequest->request_type, ['single_day', 'half_day']))
                                {{ $selectedLeaveRequest->start_date->format('l, F j, Y') }} ({{ $selectedLeaveRequest->request_type === 'half_day' ? 'Half Day' : 'Full Day' }})
                            @else
                                {{ $selectedLeaveRequest->start_date->format('M j, Y') }} — {{ $selectedLeaveRequest->end_date->format('M j, Y') }}
                            @endif
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">My Reason</span>
                        <p class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-slate-800 whitespace-pre-wrap leading-relaxed">
                            {{ $selectedLeaveRequest->reason }}
                        </p>
                    </div>

                    <!-- Admin Review Breakdown (If reviewed) -->
                    @if($selectedLeaveRequest->status !== 'pending')
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Admin Review Information</span>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Leave Category</span>
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
                                        $label = $typeLabels[$selectedLeaveRequest->leave_category] ?? ucfirst($selectedLeaveRequest->leave_category ?? 'N/A');
                                    @endphp
                                    <span class="font-bold text-slate-800">{{ $label }}</span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Payment Type</span>
                                    <span class="font-bold text-slate-800 capitalize">{{ $selectedLeaveRequest->payment_type ?? 'N/A' }}</span>
                                </div>
                            </div>

                            @if($selectedLeaveRequest->admin_remarks)
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 space-y-0.5">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Admin Remarks</span>
                                    <p class="text-slate-800 italic">{{ $selectedLeaveRequest->admin_remarks }}</p>
                                </div>
                            @endif

                            @if($selectedLeaveRequest->reviewer)
                                <p class="text-[10px] text-slate-400">Reviewed by <strong>{{ $selectedLeaveRequest->reviewer->name }}</strong> on {{ $selectedLeaveRequest->reviewed_at?->format('M d, Y g:i A') }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                    <button 
                        wire:click="closeDetailModal" 
                        class="px-4 py-2 text-xs font-bold rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
