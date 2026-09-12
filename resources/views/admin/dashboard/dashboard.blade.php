<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 relative overflow-hidden shadow-xs">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Admin System Active
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Employee Daily Slot & Activity Control</h1>
            <p class="text-slate-600 mt-2 text-sm leading-relaxed">
                Monitor employee 2-hour slot check-ins, 1-hour lunch compliance, and 3rd slot deviations in real time with automated red highlights.
            </p>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Employees -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Staff</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalEmployees }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Registered Employees</p>
        </div>

        <!-- Metric 2: Shift Started Today -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Shift Started Today</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $clockedInToday }}</div>
            <p class="text-xs text-emerald-600 mt-1 font-medium">Active Shifts</p>
        </div>

        <!-- Metric 3: Lunch Exceeded (RED) -->
        <div class="bg-white border {{ $lunchExceededCount > 0 ? 'border-red-300 bg-red-50/20' : 'border-slate-200' }} rounded-xl p-5 hover:border-red-400 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-red-700">Exceeded Lunch (&gt;1h)</span>
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $lunchExceededCount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $lunchExceededCount }}</div>
            <p class="text-xs {{ $lunchExceededCount > 0 ? 'text-red-600 font-bold' : 'text-slate-500' }} mt-1">
                {{ $lunchExceededCount > 0 ? '⚠️ Action Required' : 'All within 1h limit' }}
            </p>
        </div>

        <!-- Metric 4: Total Red Flags (RED) -->
        <div class="bg-white border {{ $totalRedFlagsCount > 0 ? 'border-red-300 bg-red-50/20' : 'border-slate-200' }} rounded-xl p-5 hover:border-red-400 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-red-700">Total Red Flags</span>
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $totalRedFlagsCount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $totalRedFlagsCount }}</div>
            <p class="text-xs {{ $totalRedFlagsCount > 0 ? 'text-red-600 font-bold' : 'text-slate-500' }} mt-1">
                {{ $totalRedFlagsCount > 0 ? '⚠️ Deviations Detected' : 'All Shift Records Compliant' }}
            </p>
        </div>
    </div>

    <!-- Employee Daily Activity Tracking Table -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Today's Employee Slot & Activity Control Table</h3>
                <p class="text-xs text-slate-500">Live monitoring for {{ now()->format('F j, Y') }}</p>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search employee..." 
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white"
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select 
                    wire:model.live="statusFilter" 
                    class="py-2 px-3 text-xs rounded-lg border border-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white font-medium text-slate-700"
                >
                    <option value="all">All Records</option>
                    <option value="flagged">🚨 All Red Flagged Exceptions</option>
                    <option value="slot2_flagged">Slot 2 Check-In Exceeded (&gt;30m)</option>
                    <option value="lunch_exceeded">Exceeded Lunch (&gt;1h)</option>
                    <option value="slot3_flagged">3rd Slot Deviation (&gt;30m)</option>
                </select>
            </div>
        </div>

        <!-- Attendance & Activity Table -->
        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="uppercase bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Employee</th>
                        <th class="px-4 py-3">1st Clock-In & Slot 2 Check-In</th>
                        <th class="px-4 py-3">Lunch Break (1h Max)</th>
                        <th class="px-4 py-3">3rd Slot Start & End</th>
                        <th class="px-4 py-3">Compliance Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($trackings as $tr)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ ($tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged) ? 'bg-red-50/30' : '' }}">
                            <!-- Employee Info -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 text-sm">{{ $tr->user->name ?? 'Unknown' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $tr->user->email ?? '' }}</div>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $tr->user->department->name ?? 'General' }}
                                    </span>
                                </div>
                            </td>

                            <!-- 1st Clock In & Slot 2 Check-In -->
                            <td class="px-4 py-3.5 align-top space-y-1">
                                <div class="font-semibold text-slate-900">
                                    Clock In: <span class="text-blue-700 font-bold">{{ $tr->slot1_checkin_time ? $tr->slot1_checkin_time?->format('g:i A') : 'Pending' }}</span>
                                </div>

                                @if($tr->slot2_is_flagged)
                                    <!-- RED BADGE for Slot 2 Checkin Exceeded > 30m -->
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-xs">
                                        <span>⚠️ Exceeded 30-min Window (+{{ $tr->slot2_deviation_minutes }}m)</span>
                                    </div>
                                @elseif($tr->slot2_checkin_time)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span>✓ Check-in at {{ $tr->slot2_checkin_time?->format('g:i A') }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium">Slot 2 Check-in Pending</span>
                                @endif
                            </td>

                            <!-- Lunch Break Details (Highlight RED if exceeded > 60m) -->
                            <td class="px-4 py-3.5 align-top space-y-1">
                                @if($tr->lunch_start_time)
                                    <div class="text-slate-900 font-medium">
                                        {{ $tr->lunch_start_time?->format('g:i A') }}
                                        →
                                        {{ $tr->lunch_end_time ? $tr->lunch_end_time?->format('g:i A') : 'In Progress' }}
                                    </div>
                                    
                                    @if($tr->lunch_exceeded)
                                        <!-- RED BADGE for Exceeded Lunch -->
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-xs">
                                            <span>⚠️ {{ $tr->lunch_duration_minutes }}m (Exceeded by +{{ $tr->lunch_exceeded_minutes }}m)</span>
                                        </div>
                                    @elseif($tr->lunch_end_time)
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span>✓ {{ $tr->lunch_duration_minutes }} mins (On Time)</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                            <span>Lunching...</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 font-medium">Not Started</span>
                                @endif
                            </td>

                            <!-- 3rd Slot Details (Highlight RED if >30 min early or exceeded) -->
                            <td class="px-4 py-3.5 align-top space-y-1">
                                @if($tr->slot3_start_time)
                                    <div class="text-slate-900 font-medium">
                                        {{ $tr->slot3_start_time?->format('g:i A') }}
                                        →
                                        {{ $tr->slot3_end_time ? $tr->slot3_end_time?->format('g:i A') : 'In Progress' }}
                                    </div>

                                    @if($tr->slot3_is_flagged)
                                        <!-- RED BADGE for 3rd Slot Exceeded > 30m -->
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-xs">
                                            <span>⚠️ Exceeded 3rd Slot Time (+{{ $tr->slot3_deviation_minutes }}m &gt; 30m threshold)</span>
                                        </div>
                                    @elseif($tr->slot3_end_time)
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span>✓ Completed On Schedule</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 font-medium">Not Started</span>
                                @endif
                            </td>

                            <!-- Overall Status -->
                            <td class="px-4 py-3.5 align-top">
                                @if($tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged)
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-red-100 text-red-800 border border-red-300 block text-center uppercase tracking-wide">
                                        🚨 Red Flagged
                                    </span>
                                @elseif($tr->slot3_end_time)
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 block text-center uppercase tracking-wide">
                                        ✓ Shift Compliant
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200 block text-center uppercase tracking-wide">
                                        Shift Active
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-bold text-slate-700">No daily slot tracking entries found for today.</p>
                                    <p class="text-xs text-slate-400">Employee clock-ins and slot activities will appear here in real time.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
