<div class="space-y-6" wire:poll.10s>
    <!-- Header Card with View Switcher -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="ri-calendar-check-line text-slate-500"></i>
                        Attendance & Working Hours Calendar
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Working Hours & Slot History</h1>
                <p class="text-slate-500 mt-1 text-xs sm:text-sm">Clean monthly calendar view showing your present days, leaves, worked hours, and 4-slot progress chains.</p>
            </div>

            <!-- View Toggle Switcher -->
            <div class="flex items-center gap-1.5 bg-slate-100/80 p-1.5 rounded-md border border-slate-200/80 shrink-0">
                <button 
                    wire:click="setViewMode('calendar')" 
                    class="px-3.5 py-2 rounded-md text-xs font-semibold transition-all shadow-2xs cursor-pointer flex items-center gap-1.5 {{ $viewMode === 'calendar' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    <i class="ri-calendar-event-line"></i>
                    <span>Calendar View</span>
                </button>

                <button 
                    wire:click="setViewMode('list')" 
                    class="px-3.5 py-2 rounded-md text-xs font-semibold transition-all shadow-2xs cursor-pointer flex items-center gap-1.5 {{ $viewMode === 'list' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    <i class="ri-list-check-2"></i>
                    <span>List History</span>
                </button>
            </div>
        </div>
    </div>

    <!-- View Mode 1: Calendar View -->
    @if($viewMode === 'calendar')
        <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-6">
            <!-- Calendar Navigation & Summary Bar -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 rounded-md bg-slate-50/80 border border-slate-200/90">
                <!-- Left: Month Navigation -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <button 
                        wire:click="previousMonth" 
                        class="w-8 h-8 rounded-md bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                        title="Previous Month"
                    >
                        <i class="ri-arrow-left-s-line text-lg"></i>
                    </button>

                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight min-w-[130px] text-center">
                        {{ $currentMonthLabel }}
                    </h2>

                    <button 
                        wire:click="nextMonth" 
                        class="w-8 h-8 rounded-md bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                        title="Next Month"
                    >
                        <i class="ri-arrow-right-s-line text-lg"></i>
                    </button>

                    <button 
                        wire:click="goToToday" 
                        class="px-2.5 py-1.5 rounded-md text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer ml-1"
                    >
                        Today
                    </button>
                </div>

                <!-- Right: Monthly Attendance Summary Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Present: <strong>{{ $presentDaysCount }}d</strong>
                    </span>

                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Leave: <strong>{{ $leaveDaysCount }}d</strong>
                    </span>

                    @if(isset($halfDayDaysCount) && $halfDayDaysCount > 0)
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Half Day: <strong>{{ $halfDayDaysCount }}d</strong>
                        </span>
                    @endif

                    @if($absentDaysCount > 0)
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-800 border border-red-200 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            Absent: <strong>{{ $absentDaysCount }}d</strong>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Calendar Days Grid -->
            <div class="space-y-2">
                <!-- Day of Week Header Grid -->
                <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold uppercase tracking-wider text-slate-500 py-2 border-b border-slate-100">
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div class="text-slate-400">Sat</div>
                    <div class="text-slate-400">Sun</div>
                </div>

                <!-- 7-Column Days Grid -->
                <div class="grid grid-cols-7 gap-2">
                    @foreach($calendarDays as $day)
                        @php
                            $st = $day['status'];
                            $tr = $day['tracking'];
                            $att = $day['attendance'];
                            $isFlagged = $tr && ($tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged || $tr->slot4_is_flagged);
                        @endphp
                        <div 
                            wire:click="openDateModal('{{ $day['date'] }}')" 
                            class="min-h-[100px] p-2.5 rounded-md border transition-all cursor-pointer flex flex-col justify-between relative group
                            {{ !$day['isCurrentMonth'] ? 'bg-slate-50/40 opacity-40 border-slate-100' : '' }}
                            {{ $day['isToday'] ? 'ring-2 ring-slate-900 border-slate-900 bg-slate-50/50' : '' }}
                            {{ $day['isCurrentMonth'] && !$day['isToday'] ? 'bg-white border-slate-200 hover:border-slate-400 hover:shadow-xs' : '' }}
                            "
                        >
                            <!-- Day Number -->
                            <div class="flex items-start justify-between">
                                <span class="text-xs font-bold {{ $day['isToday'] ? 'text-slate-900 bg-slate-200 px-1.5 py-0.5 rounded-md' : ($day['isCurrentMonth'] ? 'text-slate-900' : 'text-slate-400') }}">
                                    {{ $day['dayNumber'] }}
                                </span>

                                @if($day['isToday'])
                                    <span class="text-[9px] font-bold uppercase text-slate-700 bg-slate-100 px-1 py-0.5 rounded-md">Today</span>
                                @endif
                            </div>

                            <!-- Attendance Badge -->
                            <div class="mt-2 space-y-1">
                                @if($st === 'present')
                                    <div class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-between gap-1">
                                        <span class="truncate">✓ Present</span>
                                        @if($day['formattedHours'])
                                            <span class="text-[9px] font-bold opacity-90 shrink-0">{{ $day['formattedHours'] }}</span>
                                        @endif
                                    </div>
                                @elseif($st === 'on_leave' || $st === 'leave')
                                    <div class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                                        <span>🏖 On Leave</span>
                                    </div>
                                @elseif($st === 'half_day')
                                    <div class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-between gap-1">
                                        <span>½ Half Day</span>
                                    </div>
                                @elseif($st === 'holiday')
                                    <div class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1">
                                        <span>🎉 Holiday</span>
                                    </div>
                                @elseif($st === 'absent')
                                    <div class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200 flex items-center gap-1">
                                        <span>✖ Absent</span>
                                    </div>
                                @elseif($day['isWeekend'])
                                    <div class="px-2 py-0.5 rounded-md text-[10px] font-medium text-slate-400 bg-slate-100 text-center">
                                        Off Day
                                    </div>
                                @else
                                    <div class="text-[10px] text-slate-300 text-center">
                                        —
                                    </div>
                                @endif

                                <!-- Slot Progress Stepper Pill -->
                                @if($tr && $st === 'present')
                                    <div class="flex items-center justify-center gap-0.5 pt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Step 1: Clock In"></span>
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tr->slot2_is_flagged ? 'bg-red-500' : ($tr->slot2_checkin_time ? 'bg-emerald-500' : 'bg-slate-300') }}" title="Step 2: Slot 2"></span>
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tr->lunch_exceeded ? 'bg-red-500' : ($tr->lunch_end_time ? 'bg-emerald-500' : 'bg-slate-300') }}" title="Step 3: Lunch"></span>
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tr->slot3_start_time ? 'bg-emerald-500' : 'bg-slate-300' }}" title="Step 4: 3rd Slot"></span>
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tr->slot4_is_flagged ? 'bg-red-500' : ($tr->slot4_checkin_time ? 'bg-emerald-500' : 'bg-slate-300') }}" title="Step 5: 4th Slot & Clock Out"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- View Mode 2: List History View -->
    @if($viewMode === 'list')
        <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-4">
            <!-- Date Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-md bg-slate-50/80 border border-slate-200/90">
                <div class="flex items-center gap-2">
                    <i class="ri-calendar-event-line text-slate-400"></i>
                    <label class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Filter by Date:</label>
                    <input 
                        type="date" 
                        wire:model.live="dateFilter" 
                        class="py-1.5 px-3 text-xs font-semibold rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white text-slate-900 shadow-2xs"
                    />
                </div>

                @if($dateFilter)
                    <button 
                        wire:click="resetFilters" 
                        class="px-3 py-1.5 rounded-md text-xs font-medium text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors"
                    >
                        <i class="ri-refresh-line"></i> Clear Date Filter
                    </button>
                @endif
            </div>

            <!-- Slot Chain Records Table -->
            <div class="overflow-x-auto border border-slate-200 rounded-md">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="uppercase bg-slate-50/80 text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Tracking Date</th>
                            <th class="px-4 py-3">Net Worked Hours</th>
                            <th class="px-4 py-3">Daily Slot Progress Chain</th>
                            <th class="px-4 py-3">Shift Status</th>
                            <th class="px-4 py-3 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($trackings as $tr)
                            @php
                                $st = $trackingStats[$tr->id] ?? null;
                                $isFlagged = $tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged || $tr->slot4_is_flagged;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors {{ $isFlagged ? 'bg-red-50/20' : '' }}">
                                <!-- Tracking Date -->
                                <td class="px-4 py-3.5 align-middle font-semibold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <i class="ri-calendar-line text-slate-400"></i>
                                        <span>{{ $tr->tracking_date?->format('D, M d, Y') }}</span>
                                        @if($tr->tracking_date?->toDateString() === now()->toDateString())
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">Today</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Net Worked Hours -->
                                <td class="px-4 py-3.5 align-middle">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200 inline-flex items-center gap-1">
                                        <i class="ri-time-line text-slate-400"></i>
                                        <span>{{ $st['formattedHours'] ?? '0h 0m' }}</span>
                                    </span>
                                </td>

                                <!-- Slot Progress Chain Stepper -->
                                <td class="px-4 py-3.5 align-middle">
                                    <div class="flex items-center gap-1">
                                        <!-- Step 1 -->
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-white bg-slate-900" title="Step 1: Clocked In at {{ $tr->slot1_checkin_time?->format('g:i A') }}">
                                            1
                                        </span>
                                        <span class="w-2 h-0.5 bg-slate-300"></span>

                                        <!-- Step 2 -->
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $tr->slot2_is_flagged ? 'bg-red-600 text-white' : ($tr->slot2_checkin_time ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500') }}" title="Step 2: Slot 2 Check-in">
                                            2
                                        </span>
                                        <span class="w-2 h-0.5 bg-slate-300"></span>

                                        <!-- Step 3 (Lunch) -->
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $tr->lunch_exceeded ? 'bg-red-600 text-white' : ($tr->lunch_end_time ? 'bg-slate-900 text-white' : ($tr->lunch_start_time ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-500')) }}" title="Step 3: Lunch Break">
                                            3
                                        </span>
                                        <span class="w-2 h-0.5 bg-slate-300"></span>

                                        <!-- Step 4 -->
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $tr->slot3_start_time ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500' }}" title="Step 4: 3rd Slot Start">
                                            4
                                        </span>
                                        <span class="w-2 h-0.5 bg-slate-300"></span>

                                        <!-- Step 5 -->
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $tr->slot4_is_flagged ? 'bg-red-600 text-white' : ($tr->slot4_checkin_time ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500') }}" title="Step 5: 4th Slot & Clock Out">
                                            5
                                        </span>
                                    </div>
                                </td>

                                <!-- Shift Status -->
                                <td class="px-4 py-3.5 align-middle">
                                    @if($isFlagged)
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200 inline-flex items-center gap-1">
                                            <i class="ri-error-warning-line"></i> Exception Flagged
                                        </span>
                                    @elseif($tr->slot4_checkin_time)
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                            <i class="ri-checkbox-circle-line"></i> Shift Completed
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1">
                                            <i class="ri-time-line"></i> Shift Active
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Button -->
                                <td class="px-4 py-3.5 align-middle text-right">
                                    <button 
                                        wire:click="openDetailModal({{ $tr->id }})" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs inline-flex items-center gap-1.5 cursor-pointer"
                                    >
                                        <i class="ri-eye-line text-slate-400"></i>
                                        <span>View Details</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <i class="ri-calendar-line text-4xl text-slate-300 block"></i>
                                        <p class="font-semibold text-slate-700">No slot chain records found.</p>
                                        <p class="text-xs text-slate-400">Perform shift clock-ins to start recording your daily slot roadmaps.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $trackings->links() }}
            </div>
        </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedTrackingDetail)
        @php
            $dt = $selectedTrackingDetail;
            $tr = $dt['tracking'];
            $att = $dt['attendance'];
            $logs = $dt['logs'];
            $targetDate = $dt['targetDate'];
        @endphp
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md overflow-y-auto"
            x-data
            @keydown.escape.window="$wire.closeDetailModal()"
        >
            <div class="bg-white rounded-md border border-slate-200 shadow-xl max-w-xl w-full p-6 space-y-6 text-left relative max-h-[88vh] overflow-y-auto transform transition-all">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">Work Logs for {{ \Carbon\Carbon::parse($targetDate)->format('F j, Y') }}</h2>
                            @if($att && $att->status === 'present')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Present</span>
                            @elseif($att && ($att->status === 'on_leave' || $att->status === 'leave'))
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">On Leave</span>
                            @elseif($att && $att->status === 'absent')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">Absent</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Check-in timestamps, working hours, and 5-slot progress chain.</p>
                    </div>

                    <button 
                        wire:click="closeDetailModal" 
                        class="w-8 h-8 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <!-- Stats Summary Cards -->
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-md">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Worked</span>
                        <span class="text-lg font-bold text-slate-900">{{ $dt['formattedTotalHours'] }}</span>
                    </div>

                    <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-md">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Sessions & Breaks</span>
                        <span class="text-lg font-bold text-slate-900">{{ $dt['sessionCount'] }} Session(s)</span>
                    </div>
                </div>

                <!-- 5-Step Slot Progress Chain Stepper -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">5-Step Slot Roadmap Progress</h3>
                    
                    @if($tr)
                        <div class="bg-slate-50 border border-slate-200/70 rounded-md p-4">
                            <div class="grid grid-cols-5 gap-2 text-center">
                                <!-- Step 1 -->
                                <div class="space-y-1">
                                    <div class="w-7 h-7 rounded-md bg-slate-900 text-white font-bold text-xs flex items-center justify-center mx-auto">1</div>
                                    <div class="text-[10px] font-semibold text-slate-800">Clock-In</div>
                                    <div class="text-[9px] text-slate-500">{{ $tr->slot1_checkin_time ? $tr->slot1_checkin_time?->format('g:i A') : 'Pending' }}</div>
                                </div>

                                <!-- Step 2 -->
                                <div class="space-y-1">
                                    <div class="w-7 h-7 rounded-md font-bold text-xs flex items-center justify-center mx-auto {{ $tr->slot2_is_flagged ? 'bg-red-600 text-white' : ($tr->slot2_checkin_time ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500') }}">2</div>
                                    <div class="text-[10px] font-semibold text-slate-800">Slot 2</div>
                                    <div class="text-[9px] {{ $tr->slot2_is_flagged ? 'text-red-600 font-semibold' : 'text-slate-500' }}">{{ $tr->slot2_checkin_time ? $tr->slot2_checkin_time?->format('g:i A') : 'Pending' }}</div>
                                </div>

                                <!-- Step 3 -->
                                <div class="space-y-1">
                                    <div class="w-7 h-7 rounded-md font-bold text-xs flex items-center justify-center mx-auto {{ $tr->lunch_exceeded ? 'bg-red-600 text-white' : ($tr->lunch_end_time ? 'bg-slate-900 text-white' : ($tr->lunch_start_time ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-500')) }}">3</div>
                                    <div class="text-[10px] font-semibold text-slate-800">Lunch (1h)</div>
                                    <div class="text-[9px] {{ $tr->lunch_exceeded ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                                        @if($tr->lunch_start_time)
                                            <div>{{ $tr->lunch_start_time?->format('g:i A') }} - {{ $tr->lunch_end_time ? $tr->lunch_end_time?->format('g:i A') : 'Active' }}</div>
                                            <div class="opacity-80">
                                                @if($tr->lunch_end_time)
                                                    ({{ !is_null($tr->lunch_duration_minutes) ? $tr->lunch_duration_minutes.'m' : 'Completed' }})
                                                @else
                                                    (Active)
                                                @endif
                                            </div>
                                        @else
                                            Pending
                                        @endif
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div class="space-y-1">
                                    <div class="w-7 h-7 rounded-md font-bold text-xs flex items-center justify-center mx-auto {{ $tr->slot3_start_time ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500' }}">4</div>
                                    <div class="text-[10px] font-semibold text-slate-800">3rd Slot</div>
                                    <div class="text-[9px] text-slate-500">{{ $tr->slot3_start_time ? $tr->slot3_start_time?->format('g:i A') : 'Pending' }}</div>
                                </div>

                                <!-- Step 5 -->
                                <div class="space-y-1">
                                    <div class="w-7 h-7 rounded-md font-bold text-xs flex items-center justify-center mx-auto {{ $tr->slot4_is_flagged ? 'bg-red-600 text-white' : ($tr->slot4_checkin_time ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500') }}">5</div>
                                    <div class="text-[10px] font-semibold text-slate-800">4th Slot</div>
                                    <div class="text-[9px] {{ $tr->slot4_is_flagged ? 'text-red-600 font-semibold' : 'text-slate-500' }}">{{ $tr->slot4_checkin_time ? $tr->slot4_checkin_time?->format('g:i A') : 'Pending' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 text-center text-slate-400 text-xs bg-slate-50 border border-slate-200/60 rounded-md">
                            No slot structure tracking initialized for {{ \Carbon\Carbon::parse($targetDate)->format('M d, Y') }}.
                        </div>
                    @endif
                </div>

                <!-- Clock Sessions List -->
                <div class="space-y-2.5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Clock Sessions & Breaks</h3>

                    <div class="border border-slate-200/70 rounded-md overflow-hidden divide-y divide-slate-100">
                        @forelse($logs as $idx => $log)
                            <div class="p-3 bg-white flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800">Session #{{ $idx + 1 }}: {{ $log->clock_in_time?->format('g:i A') }} → {{ $log->clock_out_time ? $log->clock_out_time?->format('g:i A') : 'Active' }}</span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                    {{ floor(($log->duration_minutes ?: $log->clock_in_time->diffInMinutes($log->clock_out_time ?: now())) / 60) }}h {{ ($log->duration_minutes ?: $log->clock_in_time->diffInMinutes($log->clock_out_time ?: now())) % 60 }}m
                                </span>
                            </div>
                        @empty
                            <div class="p-3 text-center text-slate-400 text-xs">No raw session logs available.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                    <button 
                        wire:click="closeDetailModal" 
                        class="px-4 py-2 text-xs font-semibold rounded-md border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
