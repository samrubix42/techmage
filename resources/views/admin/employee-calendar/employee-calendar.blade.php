<div class="p-6 space-y-6">
    <!-- Header Navigation & Back Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.employees') }}" wire:navigate class="hover:text-amber-600 font-medium transition-colors">Employee Management</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                <span class="text-slate-800 font-semibold">Attendance & Leave Calendar</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ri-calendar-event-fill text-amber-600"></i>
                <span>{{ $user->name }}'s Calendar</span>
            </h1>
        </div>

        <a href="{{ route('admin.employees') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition-colors">
            <i class="ri-arrow-left-line text-base"></i>
            <span>Back to Employees</span>
        </a>
    </div>

    <!-- Employee Profile Banner -->
    <div class="bg-white rounded-md border border-slate-200/80 shadow-xs p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-2xl shadow-xs shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="space-y-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-bold text-slate-900 truncate">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                            {{ $user->is_active ? 'Active Employee' : 'Inactive' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 capitalize">
                            {{ $user->role }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 flex items-center gap-2">
                        <i class="ri-mail-line"></i>
                        <span>{{ $user->email }}</span>
                    </p>
                    <div class="flex items-center gap-3 text-xs text-slate-600 pt-1 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 font-medium border border-slate-200">
                            <i class="ri-building-line text-slate-400"></i>
                            <span>Department: {{ $user->department?->name ?? 'Unassigned' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 font-medium border border-slate-200">
                            <i class="ri-calendar-check-line text-slate-400"></i>
                            <span>Policy: {{ $user->saturday_off_policy === 'sunday_2nd_4th_saturday' ? 'Sunday & 2nd/4th Sat Off' : 'Sunday Only Off' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Month Quick Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:w-auto w-full">
                <div class="p-3.5 bg-emerald-50/70 rounded-md border border-emerald-200/60 text-center min-w-[100px]">
                    <div class="text-xl font-bold text-emerald-700">{{ $presentDaysCount }}</div>
                    <div class="text-[11px] font-medium text-emerald-600">Present Days</div>
                </div>
                <div class="p-3.5 bg-amber-50/70 rounded-md border border-amber-200/60 text-center min-w-[100px]">
                    <div class="text-xl font-bold text-amber-700">{{ $leaveDaysCount }}</div>
                    <div class="text-[11px] font-medium text-amber-600">Full Leave Days</div>
                </div>
                <div class="p-3.5 bg-blue-50/70 rounded-md border border-blue-200/60 text-center min-w-[100px]">
                    <div class="text-xl font-bold text-blue-700">{{ $halfDayDaysCount }}</div>
                    <div class="text-[11px] font-medium text-blue-600">Half Days</div>
                </div>
                <div class="p-3.5 bg-red-50/70 rounded-md border border-red-200/60 text-center min-w-[100px]">
                    <div class="text-xl font-bold text-red-700">{{ $absentDaysCount }}</div>
                    <div class="text-[11px] font-medium text-red-600">Absent Days</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Controls & View Switcher Bar -->
    <div class="bg-white rounded-md border border-slate-200/80 shadow-xs p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Month Navigator -->
        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-md">
                <button 
                    wire:click="previousMonth" 
                    class="p-2 rounded-md hover:bg-white text-slate-600 hover:text-slate-900 transition-all cursor-pointer"
                    title="Previous Month"
                >
                    <i class="ri-arrow-left-s-line text-lg"></i>
                </button>
                <button 
                    wire:click="goToToday" 
                    class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-700 hover:bg-white transition-all cursor-pointer"
                >
                    Today
                </button>
                <button 
                    wire:click="nextMonth" 
                    class="p-2 rounded-md hover:bg-white text-slate-600 hover:text-slate-900 transition-all cursor-pointer"
                    title="Next Month"
                >
                    <i class="ri-arrow-right-s-line text-lg"></i>
                </button>
            </div>
            <h3 class="text-lg font-bold text-slate-900">{{ $currentMonthLabel }}</h3>
        </div>

        <!-- View Switcher -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-md w-full sm:w-auto">
            <button 
                wire:click="setViewMode('calendar')" 
                class="flex-1 sm:flex-initial px-4 py-2 rounded-md text-xs font-semibold transition-all cursor-pointer flex items-center justify-center gap-2 {{ $viewMode === 'calendar' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                <i class="ri-calendar-grid-line"></i>
                <span>Calendar Grid</span>
            </button>
            <button 
                wire:click="setViewMode('list')" 
                class="flex-1 sm:flex-initial px-4 py-2 rounded-md text-xs font-semibold transition-all cursor-pointer flex items-center justify-center gap-2 {{ $viewMode === 'list' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                <i class="ri-list-check-2"></i>
                <span>Log Table View</span>
            </button>
        </div>
    </div>

    @if ($viewMode === 'calendar')
        <!-- Calendar Grid Card -->
        <div class="bg-white rounded-md border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Day Names Header -->
            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50/80 text-center text-xs font-bold text-slate-600 uppercase tracking-wider py-3">
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div class="text-amber-700">Sat</div>
                <div class="text-red-700">Sun</div>
            </div>

            <!-- Calendar Days Grid -->
            <div class="grid grid-cols-7 auto-rows-fr divide-x divide-y divide-slate-100 bg-slate-50/30">
                @foreach ($calendarDays as $day)
                    <div 
                        wire:click="openDateModal('{{ $day['date'] }}')"
                        class="min-h-[110px] p-2 transition-all cursor-pointer relative group flex flex-col justify-between 
                        {{ ! $day['isCurrentMonth'] ? 'bg-slate-50/60 opacity-40' : 'bg-white hover:bg-amber-50/30 hover:border-amber-200' }}
                        {{ $day['isToday'] ? 'ring-2 ring-amber-500 ring-inset z-10' : '' }}"
                    >
                        <div class="flex items-center justify-between w-full">
                            <span class="text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center {{ $day['isToday'] ? 'bg-amber-600 text-white shadow-xs' : ($day['isCurrentMonth'] ? 'text-slate-700' : 'text-slate-400') }}">
                                {{ $day['dayNumber'] }}
                            </span>

                            @if ($day['formattedHours'])
                                <span class="text-[10px] font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                    <i class="ri-time-line"></i> {{ $day['formattedHours'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Status Badges -->
                        <div class="mt-2 space-y-1">
                            @if ($day['status'] === 'present')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 w-full justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Present
                                </span>
                            @elseif ($day['status'] === 'on_leave' || $day['status'] === 'leave')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 w-full justify-center">
                                    <span>🏖</span> On Leave
                                </span>
                            @elseif ($day['status'] === 'half_day')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 w-full justify-center">
                                    <span>½</span> Half Day
                                </span>
                            @elseif ($day['status'] === 'absent')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200 w-full justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Absent
                                </span>
                            @elseif ($day['status'] === 'weekend')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500 w-full justify-center">
                                    Off Day
                                </span>
                            @elseif ($day['status'] === 'holiday')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200 w-full justify-center">
                                    🎉 Holiday
                                </span>
                            @endif

                            @if ($day['leaveRequest'] && ($day['status'] === 'on_leave' || $day['status'] === 'leave' || $day['status'] === 'half_day'))
                                <div class="text-[9px] font-medium text-amber-800 text-center truncate px-1">
                                    {{ ucfirst(str_replace('_', ' ', $day['leaveRequest']->leave_category ?: 'Approved Leave')) }}
                                </div>
                            @endif
                        </div>

                        <!-- Hover Inspect Hint -->
                        <div class="text-[9px] text-amber-700 text-center opacity-0 group-hover:opacity-100 transition-opacity font-medium pt-1">
                            Click to inspect
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Log Table View -->
        <div class="bg-white rounded-md border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Monthly Attendance & Leave Log List</h3>
                <span class="text-xs text-slate-500 font-medium">{{ $monthlyLogs->count() }} Recorded Days</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Clock In</th>
                            <th class="px-6 py-3">Clock Out</th>
                            <th class="px-6 py-3">Worked Hours</th>
                            <th class="px-6 py-3">Leave / Notes</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse ($monthlyLogs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $log->attendance_date->format('d M Y, D') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($log->status === 'present')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Present
                                        </span>
                                    @elseif ($log->status === 'on_leave' || $log->status === 'leave')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            🏖 On Leave
                                        </span>
                                    @elseif ($log->status === 'half_day')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            ½ Half Day
                                        </span>
                                    @elseif ($log->status === 'absent')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Absent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">
                                            {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $log->clock_in_time ? $log->clock_in_time->format('h:i A') : '--:--' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $log->clock_out_time ? $log->clock_out_time->format('h:i A') : '--:--' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $log->total_work_minutes > 0 ? floor($log->total_work_minutes / 60).'h '.($log->total_work_minutes % 60).'m' : '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 max-w-xs truncate">
                                    {{ $log->leave_category ? ucfirst(str_replace('_', ' ', $log->leave_category)) : ($log->notes ?: '-') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button 
                                        wire:click="openDateModal('{{ $log->attendance_date->toDateString() }}')"
                                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-md font-semibold text-[11px] transition-colors cursor-pointer"
                                    >
                                        Inspect
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <i class="ri-calendar-close-line text-3xl text-slate-300 block mb-2"></i>
                                    <span>No attendance or leave logs recorded for this month.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Day Details Modal -->
    @if ($showDetailModal && $selectedDateDetail)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-md shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Attendance & Session Details</span>
                        <h3 class="text-lg font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($selectedDateDetail['targetDate'])->format('F j, Y (l)') }}
                        </h3>
                    </div>
                    <button wire:click="closeDetailModal" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                    <!-- Summary Stats Bar -->
                    <div class="grid grid-cols-3 gap-3 p-3 bg-slate-50 rounded-md border border-slate-200 text-center">
                        <div>
                            <div class="text-[10px] font-semibold text-slate-500 uppercase">Status</div>
                            <div class="text-xs font-bold text-slate-800 capitalize mt-0.5">
                                {{ str_replace('_', ' ', $selectedDateDetail['attendance']?->status ?: ($selectedDateDetail['tracking'] ? 'Present' : 'Not Logged')) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] font-semibold text-slate-500 uppercase">Worked Hours</div>
                            <div class="text-xs font-bold text-slate-900 mt-0.5">
                                {{ $selectedDateDetail['formattedTotalHours'] }}
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] font-semibold text-slate-500 uppercase">Break Duration</div>
                            <div class="text-xs font-bold text-slate-700 mt-0.5">
                                {{ $selectedDateDetail['totalBreaksMinutes'] }} mins
                            </div>
                        </div>
                    </div>

                    <!-- Leave Details (If on leave) -->
                    @if ($selectedDateDetail['leaveRequest'])
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-md space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                                    <i class="ri-calendar-event-line text-amber-600"></i> Approved Leave Details
                                </span>
                                <span class="px-2 py-0.5 rounded bg-amber-200 text-amber-900">
                                    {{ ucfirst(str_replace('_', ' ', $selectedDateDetail['leaveRequest']->leave_category ?: 'Approved Leave')) }}
                                </span>
                            </div>
                            <div class="text-xs text-amber-800 space-y-1 pt-1">
                                <p><strong>Duration:</strong> {{ $selectedDateDetail['leaveRequest']->start_date->format('M d, Y') }} to {{ $selectedDateDetail['leaveRequest']->end_date->format('M d, Y') }} ({{ $selectedDateDetail['leaveRequest']->total_days }} days)</p>
                                <p><strong>Reason:</strong> {{ $selectedDateDetail['leaveRequest']->reason ?: 'No reason provided' }}</p>
                                @if ($selectedDateDetail['leaveRequest']->admin_remarks)
                                    <p><strong>Admin Remarks:</strong> {{ $selectedDateDetail['leaveRequest']->admin_remarks }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Clock In / Out Log Sessions -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-history-line text-slate-500"></i> Clock Sessions ({{ $selectedDateDetail['sessionCount'] }})
                        </h4>

                        @if ($selectedDateDetail['logs']->isNotEmpty())
                            <div class="space-y-2">
                                @foreach ($selectedDateDetail['logs'] as $index => $log)
                                    <div class="p-3 rounded-md border border-slate-200 bg-white flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px]">
                                                {{ $index + 1 }}
                                            </span>
                                            <div>
                                                <span class="font-semibold text-slate-800">In:</span> {{ $log->clock_in_time->format('h:i:s A') }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-800">Out:</span> 
                                            {{ $log->clock_out_time ? $log->clock_out_time->format('h:i:s A') : 'Active' }}
                                        </div>
                                        <div class="font-bold text-slate-900">
                                            {{ $log->clock_out_time ? floor(($log->duration_minutes ?: $log->clock_in_time->diffInMinutes($log->clock_out_time)) / 60).'h '.($log->duration_minutes ?: $log->clock_in_time->diffInMinutes($log->clock_out_time)) % 60 .'m' : 'In Progress' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400 text-xs bg-slate-50 rounded-md border border-dashed border-slate-200">
                                No detailed clock session logs recorded for this day.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button wire:click="closeDetailModal" class="px-4 py-2 rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
