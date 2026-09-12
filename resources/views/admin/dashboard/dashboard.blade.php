<div class="space-y-6" wire:poll.10s>
    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Employees -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Staff</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i class="ri-team-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalEmployees }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Registered Staff</p>
        </div>

        <!-- Metric 2: Shift Started -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Shifts Clocked In</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="ri-user-follow-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $clockedInToday }}</div>
            <p class="text-xs text-emerald-600 mt-1 font-medium">For {{ \Carbon\Carbon::parse($selectedDate ?: now())->format('M d') }}</p>
        </div>

        <!-- Metric 3: Lunch Exceeded (RED) -->
        <div class="bg-white border {{ $lunchExceededCount > 0 ? 'border-red-300 bg-red-50/20' : 'border-slate-200' }} rounded-xl p-5 hover:border-red-400 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-red-700">Exceeded Lunch (&gt;1h)</span>
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">
                    <i class="ri-cup-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $lunchExceededCount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $lunchExceededCount }}</div>
            <p class="text-xs {{ $lunchExceededCount > 0 ? 'text-red-600 font-bold' : 'text-slate-500' }} mt-1">
                {{ $lunchExceededCount > 0 ? 'Action Required' : 'All within 1h limit' }}
            </p>
        </div>

        <!-- Metric 4: Total Red Flags (RED) -->
        <div class="bg-white border {{ $totalRedFlagsCount > 0 ? 'border-red-300 bg-red-50/20' : 'border-slate-200' }} rounded-xl p-5 hover:border-red-400 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-red-700">Total Red Flags</span>
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">
                    <i class="ri-alarm-warning-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $totalRedFlagsCount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $totalRedFlagsCount }}</div>
            <p class="text-xs {{ $totalRedFlagsCount > 0 ? 'text-red-600 font-bold' : 'text-slate-500' }} mt-1">
                {{ $totalRedFlagsCount > 0 ? 'Deviations Flagged' : 'All Compliant' }}
            </p>
        </div>
    </div>

    <!-- Employee Daily Activity Tracking Table -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
        <!-- Date & Search Filter Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
            <!-- Left: Date Picker & Quick Presets -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <i class="ri-calendar-event-line text-slate-500"></i>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Filter Date:</label>
                    <input 
                        type="date" 
                        wire:model.live="selectedDate" 
                        class="py-1.5 px-3 text-xs font-bold rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white text-slate-900 shadow-xs"
                    />
                </div>

                <div class="flex items-center gap-1.5">
                    <button 
                        wire:click="setToday" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs {{ $selectedDate === now()->toDateString() ? 'bg-amber-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}"
                    >
                        Today
                    </button>
                    <button 
                        wire:click="setYesterday" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-xs {{ $selectedDate === now()->subDay()->toDateString() ? 'bg-amber-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}"
                    >
                        Yesterday
                    </button>
                </div>
            </div>

            <!-- Right: Search & Status Dropdown -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search employee..." 
                        class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white"
                    />
                    <i class="ri-search-line text-slate-400 absolute left-3 top-2 text-sm"></i>
                </div>

                <select 
                    wire:model.live="statusFilter" 
                    class="py-1.5 px-3 text-xs rounded-lg border border-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white font-medium text-slate-700 shadow-xs"
                >
                    <option value="all">All Records</option>
                    <option value="flagged">All Red Flagged Exceptions</option>
                    <option value="slot2_flagged">Slot 2 Check-In Exceeded (&gt;30m)</option>
                    <option value="lunch_exceeded">Exceeded Lunch (&gt;1h)</option>
                    <option value="slot3_flagged">3rd Slot Exceeded (&gt;30m)</option>
                </select>
            </div>
        </div>

        <!-- Attendance & Timing Table -->
        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="uppercase bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Employee</th>
                        <th class="px-4 py-3">Slot 1 (Clock-In)</th>
                        <th class="px-4 py-3">Slot 2 Check-In</th>
                        <th class="px-4 py-3">Lunch Break (1h Max)</th>
                        <th class="px-4 py-3">3rd Slot & Shift End</th>
                        <th class="px-4 py-3">Compliance Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($trackings as $tr)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ ($tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged) ? 'bg-red-50/30' : '' }}">
                            <!-- Employee Info -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($tr->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $tr->user->name ?? 'Unknown' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $tr->user->email ?? '' }}</div>
                                        <div class="mt-0.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $tr->user->department->name ?? 'General' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Slot 1 (Clock In Time) -->
                            <td class="px-4 py-3.5 align-top space-y-1">
                                @if($tr->slot1_checkin_time)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="ri-time-line"></i>
                                        <span>{{ $tr->slot1_checkin_time?->format('g:i A') }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium">Pending Clock-In</span>
                                @endif
                            </td>

                            <!-- Slot 2 Check-In Time -->
                            <td class="px-4 py-3.5 align-top space-y-1">
                                @if($tr->slot2_is_flagged)
                                    <!-- RED BADGE for Slot 2 Checkin Exceeded > 30m -->
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-xs">
                                        <i class="ri-error-warning-fill"></i>
                                        <span>Exceeded 30m (+{{ $tr->slot2_deviation_minutes }}m)</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500">Check-in: {{ $tr->slot2_checkin_time?->format('g:i A') }}</div>
                                @elseif($tr->slot2_checkin_time)
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ri-checkbox-circle-line"></i>
                                        <span>Check-in {{ $tr->slot2_checkin_time?->format('g:i A') }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium">Pending Slot 2</span>
                                @endif
                            </td>

                            <!-- Lunch Break Details (Highlight RED if exceeded > 60m) -->
                            <td class="px-4 py-3.5 align-top space-y-1">
                                @if($tr->lunch_start_time)
                                    <div class="text-slate-900 font-medium text-xs flex items-center gap-1">
                                        <span>{{ $tr->lunch_start_time?->format('g:i A') }}</span>
                                        <i class="ri-arrow-right-line text-slate-400"></i>
                                        <span>{{ $tr->lunch_end_time ? $tr->lunch_end_time?->format('g:i A') : 'In Progress' }}</span>
                                    </div>
                                    
                                    @if($tr->lunch_exceeded)
                                        <!-- RED BADGE for Exceeded Lunch -->
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-xs">
                                            <i class="ri-error-warning-fill"></i>
                                            <span>{{ $tr->lunch_duration_minutes }}m (Exceeded by +{{ $tr->lunch_exceeded_minutes }}m)</span>
                                        </div>
                                    @elseif($tr->lunch_end_time)
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="ri-checkbox-circle-line"></i>
                                            <span>{{ $tr->lunch_duration_minutes }} mins (On Time)</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                            <i class="ri-rest-time-line"></i>
                                            <span>Lunch Active...</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 font-medium">Not Started</span>
                                @endif
                            </td>

                            <!-- 3rd Slot Start & End Details (Highlight RED if exceeded > 30m) -->
                            <td class="px-4 py-3.5 align-top space-y-1">
                                @if($tr->slot3_start_time)
                                    <div class="text-slate-900 font-medium text-xs flex items-center gap-1">
                                        <span>{{ $tr->slot3_start_time?->format('g:i A') }}</span>
                                        <i class="ri-arrow-right-line text-slate-400"></i>
                                        <span>{{ $tr->slot3_end_time ? $tr->slot3_end_time?->format('g:i A') : 'In Progress' }}</span>
                                    </div>

                                    @if($tr->slot3_is_flagged)
                                        <!-- RED BADGE for 3rd Slot Exceeded > 30m -->
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-xs">
                                            <i class="ri-error-warning-fill"></i>
                                            <span>Exceeded Slot 3 (+{{ $tr->slot3_deviation_minutes }}m)</span>
                                        </div>
                                    @elseif($tr->slot3_end_time)
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="ri-checkbox-circle-line"></i>
                                            <span>Shift Completed</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 font-medium">Not Started</span>
                                @endif
                            </td>

                            <!-- Overall Compliance Status -->
                            <td class="px-4 py-3.5 align-top">
                                @if($tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged)
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-red-100 text-red-800 border border-red-300 flex items-center justify-center gap-1 uppercase tracking-wide shadow-xs">
                                        <i class="ri-alarm-warning-fill"></i>
                                        <span>Red Flagged</span>
                                    </span>
                                @elseif($tr->slot3_end_time)
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center justify-center gap-1 uppercase tracking-wide">
                                        <i class="ri-checkbox-circle-fill"></i>
                                        <span>Shift Compliant</span>
                                    </span>
                                @elseif($tr->slot1_checkin_time)
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200 flex items-center justify-center gap-1 uppercase tracking-wide">
                                        <i class="ri-time-fill"></i>
                                        <span>Shift Active</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center gap-1 uppercase tracking-wide">
                                        <span>Not Started</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <i class="ri-calendar-line text-4xl text-slate-300 block"></i>
                                    <p class="font-bold text-slate-700">No slot tracking entries found for {{ \Carbon\Carbon::parse($selectedDate ?: now())->format('F j, Y') }}.</p>
                                    <p class="text-xs text-slate-400">Select a different date or search query above to view employee shift timings.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
