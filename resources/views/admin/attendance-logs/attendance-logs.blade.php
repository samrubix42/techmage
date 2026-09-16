<div class="space-y-6" wire:poll.10s>
    <!-- Page Header -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <i class="ri-time-line text-amber-600"></i>
                        Employee Working Hours & Slot Portal
                    </span>
                    <span class="text-xs text-slate-500 font-medium">{{ \Carbon\Carbon::parse($selectedDate ?: now())->format('l, F j, Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Employee Working Hours & Slot Tracking</h1>
                <p class="text-slate-600 mt-1 text-sm">Monitor calculated employee working hours across break clock-outs and 4-step slot progress chains.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-slate-50 border border-slate-200 rounded-md px-4 py-2 text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Staff Tracked</span>
                    <span class="text-lg font-bold text-slate-900">{{ $totalStaff }} Employees</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Staff -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Staff Count</span>
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i class="ri-team-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalStaff }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Registered Employees</p>
        </div>

        <!-- Metric 2: Clocked In -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Clocked In</span>
                <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="ri-user-check-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalClockedInCount }}</div>
            <p class="text-xs text-emerald-600 mt-1 font-medium">Active on {{ \Carbon\Carbon::parse($selectedDate ?: now())->format('M d') }}</p>
        </div>

        <!-- Metric 3: Avg Working Hours -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Avg Work Hours</span>
                <div class="w-8 h-8 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i class="ri-time-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $avgHours }} hrs</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Per Clocked-In Staff</p>
        </div>

        <!-- Metric 4: Red Flag Exceptions -->
        <div class="bg-white border {{ $totalFlaggedCount > 0 ? 'border-red-300 bg-red-50/20' : 'border-slate-200/80' }} rounded-md p-5 hover:border-red-400 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-red-700">Red Flagged</span>
                <div class="w-8 h-8 rounded-md bg-red-100 text-red-700 flex items-center justify-center font-bold">
                    <i class="ri-alarm-warning-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $totalFlaggedCount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $totalFlaggedCount }}</div>
            <p class="text-xs {{ $totalFlaggedCount > 0 ? 'text-red-600 font-bold' : 'text-slate-500' }} mt-1">
                {{ $totalFlaggedCount > 0 ? 'Timing Exceptions Flagged' : 'All Compliant' }}
            </p>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-4">
        <!-- Date & Filters Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 rounded-md bg-slate-50 border border-slate-200">
            <!-- Left: Date Selector & Quick Presets -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <i class="ri-calendar-event-line text-slate-500"></i>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Select Date:</label>
                    <input 
                        type="date" 
                        wire:model.live="selectedDate" 
                        class="py-1.5 px-3 text-xs font-bold rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white text-slate-900 shadow-xs"
                    />
                </div>

                <div class="flex items-center gap-1.5">
                    <button 
                        wire:click="setToday" 
                        class="px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-xs {{ $selectedDate === now()->toDateString() ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}"
                    >
                        Today
                    </button>
                    <button 
                        wire:click="setYesterday" 
                        class="px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-xs {{ $selectedDate === now()->subDay()->toDateString() ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}"
                    >
                        Yesterday
                    </button>
                </div>
            </div>

            <!-- Right: Search, Department & Reset -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search employee..." 
                        class="w-full pl-9 pr-3 py-1.5 text-xs rounded-md border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white"
                    />
                    <i class="ri-search-line text-slate-400 absolute left-3 top-2 text-sm"></i>
                </div>

                <select 
                    wire:model.live="departmentFilter" 
                    class="py-1.5 px-3 text-xs rounded-md border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white font-medium text-slate-700 shadow-xs"
                >
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                @if($search || $departmentFilter || $selectedDate !== now()->toDateString())
                    <button 
                        wire:click="resetFilters" 
                        class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors"
                        title="Reset Filters"
                    >
                        <i class="ri-refresh-line"></i> Reset
                    </button>
                @endif
            </div>
        </div>

        <!-- Attendance & Work Hours Table -->
        <div class="overflow-x-auto border border-slate-200/80 rounded-md">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="uppercase bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Employee</th>
                        <th class="px-4 py-3">Clock Log Sessions</th>
                        <th class="px-4 py-3">Net Working Hours</th>
                        <th class="px-4 py-3">Break Duration</th>
                        <th class="px-4 py-3">Progress Chain Status</th>
                        <th class="px-4 py-3 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $emp)
                        @php
                            $st = $employeeStats[$emp->id] ?? null;
                            $tr = $st['tracking'] ?? null;
                            $isFlagged = $tr && ($tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $isFlagged ? 'bg-red-50/30' : '' }}">
                            <!-- Employee Info -->
                            <td class="px-4 py-3.5 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $emp->name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $emp->email }}</div>
                                        <div class="mt-0.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $emp->department->name ?? 'General' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Clock Log Sessions -->
                            <td class="px-4 py-3.5 align-middle space-y-1">
                                @if($st && $st['firstClockIn'])
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-800">
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[11px]">
                                            In: {{ $st['firstClockIn']?->format('g:i A') }}
                                        </span>
                                        <i class="ri-arrow-right-line text-slate-400"></i>
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 font-bold text-[11px]">
                                            Out: {{ $st['lastClockOut'] ? $st['lastClockOut']?->format('g:i A') : ($st['hasActiveSession'] ? 'Active' : 'Pending') }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        Total Sessions: <strong>{{ $st['sessionCount'] }}</strong> {{ $st['sessionCount'] > 1 ? '(Breaks taken)' : '' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium">Not Clocked In</span>
                                @endif
                            </td>

                            <!-- Net Working Hours -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($st && $st['totalWorkedMinutes'] > 0)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold shadow-2xs {{ $st['hasActiveSession'] ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-800 border border-slate-300' }}">
                                        <i class="ri-time-fill text-slate-500"></i>
                                        <span>{{ $st['formattedTotalHours'] }}</span>
                                        @if($st['hasActiveSession'])
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Active Working Session"></span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium">0h 0m</span>
                                @endif
                            </td>

                            <!-- Break Duration -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($st && $st['totalBreaksMinutes'] > 0)
                                    <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-bold text-xs inline-flex items-center gap-1">
                                        <i class="ri-rest-time-line text-amber-600"></i>
                                        <span>{{ $st['formattedBreakHours'] }}</span>
                                    </span>
                                @elseif($tr && $tr->lunch_duration_minutes)
                                    <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-bold text-xs inline-flex items-center gap-1">
                                        <i class="ri-cup-line text-amber-600"></i>
                                        <span>{{ $tr->lunch_duration_minutes }}m Lunch</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">0m</span>
                                @endif
                            </td>

                            <!-- Progress Chain Status -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($tr)
                                    <div class="flex items-center gap-1">
                                        <!-- Step 1 Pill -->
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white bg-emerald-600" title="Step 1: Clock In at {{ $tr->slot1_checkin_time?->format('g:i A') }}">
                                            1
                                        </span>
                                        <span class="w-3 h-0.5 bg-slate-300"></span>

                                        <!-- Step 2 Pill -->
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $tr->slot2_is_flagged ? 'bg-red-600 text-white' : ($tr->slot2_checkin_time ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500') }}" title="Step 2: Slot 2 Check-in">
                                            2
                                        </span>
                                        <span class="w-3 h-0.5 bg-slate-300"></span>

                                        <!-- Step 3 Pill (Lunch) -->
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $tr->lunch_exceeded ? 'bg-red-600 text-white' : ($tr->lunch_end_time ? 'bg-emerald-600 text-white' : ($tr->lunch_start_time ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-500')) }}" title="Step 3: Lunch Break">
                                            3
                                        </span>
                                        <span class="w-3 h-0.5 bg-slate-300"></span>

                                        <!-- Step 4 Pill (3rd Slot) -->
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $tr->slot3_is_flagged ? 'bg-red-600 text-white' : ($tr->slot3_end_time ? 'bg-emerald-600 text-white' : ($tr->slot3_start_time ? 'bg-indigo-600 text-white animate-pulse' : 'bg-slate-200 text-slate-500')) }}" title="Step 4: 3rd Slot & Shift End">
                                            4
                                        </span>
                                    </div>
                                    @if($isFlagged)
                                        <div class="mt-1 text-[10px] font-bold text-red-600 flex items-center gap-1">
                                            <i class="ri-error-warning-fill"></i> Red Flagged Exception
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-[11px] font-medium">No Slot Activity</span>
                                @endif
                            </td>

                            <!-- Action Button -->
                            <td class="px-4 py-3.5 align-middle text-right">
                                <button 
                                    wire:click="openModal({{ $emp->id }})" 
                                    class="px-3 py-1.5 rounded-md text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs inline-flex items-center gap-1.5 cursor-pointer"
                                >
                                    <i class="ri-dashboard-2-line text-slate-500"></i>
                                    <span>Inspect Details</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <i class="ri-user-search-line text-4xl text-slate-300 block"></i>
                                    <p class="font-bold text-slate-700">No employee records found for this date/filter.</p>
                                    <p class="text-xs text-slate-400">Try selecting a different date or search query above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="pt-2">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- Minimal & Clean Working Hours & Progress Chain Modal -->
    @if($showDetailModal && $selectedUserDetail)
        @php
            $u = $selectedUserDetail['user'];
            $dt = $selectedUserDetail['date'];
            $st = $selectedUserDetail;
            $tr = $st['tracking'];
            $logs = $st['logs'];
        @endphp
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md overflow-y-auto"
            x-data
            @keydown.escape.window="$wire.closeModal()"
        >
            <div class="bg-white rounded-md border border-slate-200/80 shadow-2xl max-w-2xl w-full p-6 space-y-6 text-left relative max-h-[88vh] overflow-y-auto transform transition-all">
                <!-- Clean Minimal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-md bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center font-bold text-sm shrink-0">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold text-slate-900">{{ $u->name }}</h2>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200/60">
                                    {{ $u->department->name ?? 'General' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $u->email }} • <span class="text-slate-600 font-semibold">{{ \Carbon\Carbon::parse($dt)->format('M d, Y') }}</span></p>
                        </div>
                    </div>

                    <button 
                        wire:click="closeModal" 
                        class="w-8 h-8 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                        title="Close Modal"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <!-- Minimal Key Stats Grid (Clean Cards) -->
                <div class="grid grid-cols-3 gap-3">
                    <!-- Stat 1: Total Working Hours -->
                    <div class="p-4 rounded-md bg-slate-50 border border-slate-200/70 text-center space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Worked</span>
                        <div class="text-xl font-extrabold text-slate-900">{{ $st['formattedTotalHours'] }}</div>
                        <span class="text-[10px] text-slate-500 font-medium">Net calculated</span>
                    </div>

                    <!-- Stat 2: Total Breaks -->
                    <div class="p-4 rounded-md bg-amber-50/50 border border-amber-200/60 text-center space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Break Duration</span>
                        <div class="text-xl font-extrabold text-amber-900">{{ $st['formattedBreakHours'] }}</div>
                        <span class="text-[10px] text-amber-700 font-medium">{{ $st['sessionCount'] > 1 ? ($st['sessionCount'] - 1).' break(s)' : 'No extra break' }}</span>
                    </div>

                    <!-- Stat 3: Shift Status -->
                    <div class="p-4 rounded-md bg-slate-50 border border-slate-200/70 text-center space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Shift Status</span>
                        <div class="text-sm font-bold mt-1">
                            @if($st['hasActiveSession'])
                                <span class="text-emerald-600 inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Active Now
                                </span>
                            @elseif($st['firstClockIn'])
                                <span class="text-slate-700">Clocked Out</span>
                            @else
                                <span class="text-slate-400">Not Started</span>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-500 font-medium">{{ $st['sessionCount'] }} session(s)</span>
                    </div>
                </div>

                <!-- Sleek Minimal Progress Chain Timeline -->
                <div class="space-y-3 pt-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Daily Slot Progress Chain</h3>
                        @if($tr && ($tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged))
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 flex items-center gap-1">
                                <i class="ri-error-warning-fill text-xs"></i> Flagged Exceptions
                            </span>
                        @endif
                    </div>

                    @if($tr)
                        <div class="bg-slate-50 border border-slate-200/70 rounded-md p-4 space-y-4">
                            <!-- Stepper Horizontal Connection -->
                            <div class="grid grid-cols-4 gap-2 text-center relative">
                                <!-- Step 1 -->
                                <div class="space-y-1.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center mx-auto shadow-2xs">
                                        1
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-800">Shift Clock-In</div>
                                    <div class="text-[10px] text-slate-500 font-medium">
                                        {{ $tr->slot1_checkin_time ? $tr->slot1_checkin_time?->format('g:i A') : 'Pending' }}
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div class="space-y-1.5">
                                    <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center mx-auto shadow-2xs {{ $tr->slot2_is_flagged ? 'bg-red-600 text-white' : ($tr->slot2_checkin_time ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500') }}">
                                        2
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-800">Slot 2 Check-In</div>
                                    <div class="text-[10px] {{ $tr->slot2_is_flagged ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                                        {{ $tr->slot2_checkin_time ? $tr->slot2_checkin_time?->format('g:i A') : 'Pending' }}
                                    </div>
                                    @if($tr->slot2_is_flagged)
                                        <div class="text-[9px] font-bold text-red-600 uppercase">+{{ $tr->slot2_deviation_minutes }}m Exceeded</div>
                                    @endif
                                </div>

                                <!-- Step 3 -->
                                <div class="space-y-1.5">
                                    <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center mx-auto shadow-2xs {{ $tr->lunch_exceeded ? 'bg-red-600 text-white' : ($tr->lunch_end_time ? 'bg-emerald-600 text-white' : ($tr->lunch_start_time ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-500')) }}">
                                        3
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-800">Lunch Break (1h)</div>
                                    <div class="text-[10px] {{ $tr->lunch_exceeded ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                                        {{ $tr->lunch_duration_minutes ? $tr->lunch_duration_minutes.' mins' : ($tr->lunch_start_time ? 'Active' : 'Pending') }}
                                    </div>
                                    @if($tr->lunch_exceeded)
                                        <div class="text-[9px] font-bold text-red-600 uppercase">+{{ $tr->lunch_exceeded_minutes }}m Exceeded</div>
                                    @endif
                                </div>

                                <!-- Step 4 -->
                                <div class="space-y-1.5">
                                    <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center mx-auto shadow-2xs {{ $tr->slot3_is_flagged ? 'bg-red-600 text-white' : ($tr->slot3_end_time ? 'bg-emerald-600 text-white' : ($tr->slot3_start_time ? 'bg-indigo-600 text-white animate-pulse' : 'bg-slate-200 text-slate-500')) }}">
                                        4
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-800">3rd Slot & End</div>
                                    <div class="text-[10px] {{ $tr->slot3_is_flagged ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                                        {{ $tr->slot3_end_time ? $tr->slot3_end_time?->format('g:i A') : 'Pending' }}
                                    </div>
                                    @if($tr->slot3_is_flagged)
                                        <div class="text-[9px] font-bold text-red-600 uppercase">+{{ $tr->slot3_deviation_minutes }}m Exceeded</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 text-center text-slate-400 text-xs bg-slate-50 border border-slate-200/60 rounded-md">
                            No slot structure tracking initialized for this date.
                        </div>
                    @endif
                </div>

                <!-- Clock In / Out Log Sessions (Breaks Included) -->
                <div class="space-y-2.5 pt-1">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Clock-In & Out Sessions</h3>

                    <div class="border border-slate-200/70 rounded-md overflow-hidden divide-y divide-slate-100">
                        @forelse($logs as $idx => $log)
                            <div class="p-3.5 bg-white flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 font-bold text-[11px] flex items-center justify-center shrink-0">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-slate-800 flex items-center gap-2">
                                            <span>In: {{ $log->clock_in_time?->format('g:i A') }}</span>
                                            <i class="ri-arrow-right-s-line text-slate-400"></i>
                                            <span>Out: {{ $log->clock_out_time ? $log->clock_out_time?->format('g:i A') : 'Active' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    @if($log->clock_out_time)
                                        <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px]">
                                            {{ floor(($log->duration_minutes ?: $log->clock_in_time->diffInMinutes($log->clock_out_time)) / 60) }}h {{ ($log->duration_minutes ?: $log->clock_in_time->diffInMinutes($log->clock_out_time)) % 60 }}m
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[11px] animate-pulse">
                                            In Progress
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-slate-400 text-xs">
                                No raw attendance log entries recorded for this employee on this date.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Minimal Modal Footer -->
                <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                    <button 
                        wire:click="closeModal" 
                        class="px-4 py-2 text-xs font-bold rounded-md border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
