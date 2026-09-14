<div class="space-y-6" wire:poll.10s>
    <!-- Header Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <i class="ri-history-line text-amber-600"></i>
                        My Daily Work Logs
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Working Hours & Slot History</h1>
                <p class="text-slate-600 mt-1 text-sm">Review your daily 4-slot progress roadmap, check-in timestamps, working hours, and shift history.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('employee.dashboard') }}" class="px-4 py-2 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors inline-flex items-center gap-1.5">
                    <i class="ri-dashboard-3-line"></i>
                    <span>Back to Today's Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
        <!-- Date Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
            <div class="flex items-center gap-2">
                <i class="ri-calendar-event-line text-slate-500"></i>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Filter by Date:</label>
                <input 
                    type="date" 
                    wire:model.live="dateFilter" 
                    class="py-1.5 px-3 text-xs font-bold rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white text-slate-900 shadow-xs"
                />
            </div>

            @if($dateFilter)
                <button 
                    wire:click="resetFilters" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors"
                >
                    <i class="ri-refresh-line"></i> Clear Date Filter
                </button>
            @endif
        </div>

        <!-- Slot Chain Records Table -->
        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="uppercase bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
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
                            $isFlagged = $tr->slot2_is_flagged || $tr->lunch_exceeded || $tr->slot3_is_flagged;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $isFlagged ? 'bg-red-50/20' : '' }}">
                            <!-- Tracking Date -->
                            <td class="px-4 py-3.5 align-middle font-bold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <i class="ri-calendar-line text-slate-400"></i>
                                    <span>{{ $tr->tracking_date?->format('D, M d, Y') }}</span>
                                    @if($tr->tracking_date?->toDateString() === now()->toDateString())
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Today</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Net Worked Hours -->
                            <td class="px-4 py-3.5 align-middle">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200 inline-flex items-center gap-1">
                                    <i class="ri-time-line text-slate-500"></i>
                                    <span>{{ $st['formattedHours'] ?? '0h 0m' }}</span>
                                </span>
                            </td>

                            <!-- Slot Progress Chain Stepper -->
                            <td class="px-4 py-3.5 align-middle">
                                <div class="flex items-center gap-1.5">
                                    <!-- Step 1 -->
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white bg-emerald-600" title="Step 1: Clocked In at {{ $tr->slot1_checkin_time?->format('g:i A') }}">
                                        1
                                    </span>
                                    <span class="w-3 h-0.5 bg-slate-300"></span>

                                    <!-- Step 2 -->
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $tr->slot2_is_flagged ? 'bg-red-600 text-white' : ($tr->slot2_checkin_time ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500') }}" title="Step 2: Slot 2 Check-in">
                                        2
                                    </span>
                                    <span class="w-3 h-0.5 bg-slate-300"></span>

                                    <!-- Step 3 (Lunch) -->
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $tr->lunch_exceeded ? 'bg-red-600 text-white' : ($tr->lunch_end_time ? 'bg-emerald-600 text-white' : ($tr->lunch_start_time ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-500')) }}" title="Step 3: Lunch Break">
                                        3
                                    </span>
                                    <span class="w-3 h-0.5 bg-slate-300"></span>

                                    <!-- Step 4 -->
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $tr->slot3_is_flagged ? 'bg-red-600 text-white' : ($tr->slot3_end_time ? 'bg-emerald-600 text-white' : ($tr->slot3_start_time ? 'bg-indigo-600 text-white animate-pulse' : 'bg-slate-200 text-slate-500')) }}" title="Step 4: 3rd Slot & Shift End">
                                        4
                                    </span>
                                </div>
                            </td>

                            <!-- Shift Status -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($isFlagged)
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800 border border-red-200 inline-flex items-center gap-1">
                                        <i class="ri-error-warning-fill"></i> Exception Flagged
                                    </span>
                                @elseif($tr->slot3_end_time)
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                        <i class="ri-checkbox-circle-line"></i> Shift Completed
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200 inline-flex items-center gap-1">
                                        <i class="ri-time-line"></i> Shift Active
                                    </span>
                                @endif
                            </td>

                            <!-- Action Button -->
                            <td class="px-4 py-3.5 align-middle text-right">
                                <button 
                                    wire:click="openDetailModal({{ $tr->id }})" 
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-800 bg-white hover:bg-amber-50 hover:text-amber-800 border border-slate-200 hover:border-amber-300 transition-colors shadow-2xs inline-flex items-center gap-1.5 cursor-pointer"
                                >
                                    <i class="ri-eye-line text-amber-600"></i>
                                    <span>View Details</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <i class="ri-calendar-line text-4xl text-slate-300 block"></i>
                                    <p class="font-bold text-slate-700">No slot chain records found.</p>
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

    <!-- Employee Detail Modal -->
    @if($showDetailModal && $selectedTrackingDetail)
        @php
            $dt = $selectedTrackingDetail;
            $tr = $dt['tracking'];
            $logs = $dt['logs'];
        @endphp
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/40 backdrop-blur-md overflow-y-auto"
            x-data
            @keydown.escape.window="$wire.closeDetailModal()"
        >
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-6 text-left relative max-h-[88vh] overflow-y-auto transform transition-all">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Slot Chain Details for {{ $tr->tracking_date?->format('F j, Y') }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Your daily slot check-ins and session work logs.</p>
                    </div>

                    <button 
                        wire:click="closeDetailModal" 
                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <!-- Stats Summary Cards -->
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Worked</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ $dt['formattedTotalHours'] }}</span>
                    </div>

                    <div class="p-3.5 bg-amber-50/50 border border-amber-200/60 rounded-xl">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Sessions & Breaks</span>
                        <span class="text-lg font-extrabold text-amber-900">{{ $dt['sessionCount'] }} Session(s)</span>
                    </div>
                </div>

                <!-- 4-Step Slot Progress Chain Stepper -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">4-Step Slot Roadmap Progress</h3>
                    
                    <div class="bg-slate-50 border border-slate-200/70 rounded-xl p-4">
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <!-- Step 1 -->
                            <div class="space-y-1">
                                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center mx-auto">1</div>
                                <div class="text-[11px] font-bold text-slate-800">Clock-In</div>
                                <div class="text-[10px] text-slate-500">{{ $tr->slot1_checkin_time ? $tr->slot1_checkin_time?->format('g:i A') : 'Pending' }}</div>
                            </div>

                            <!-- Step 2 -->
                            <div class="space-y-1">
                                <div class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center mx-auto {{ $tr->slot2_is_flagged ? 'bg-red-600 text-white' : ($tr->slot2_checkin_time ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500') }}">2</div>
                                <div class="text-[11px] font-bold text-slate-800">Slot 2</div>
                                <div class="text-[10px] {{ $tr->slot2_is_flagged ? 'text-red-600 font-bold' : 'text-slate-500' }}">{{ $tr->slot2_checkin_time ? $tr->slot2_checkin_time?->format('g:i A') : 'Pending' }}</div>
                            </div>

                            <!-- Step 3 -->
                            <div class="space-y-1">
                                <div class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center mx-auto {{ $tr->lunch_exceeded ? 'bg-red-600 text-white' : ($tr->lunch_end_time ? 'bg-emerald-600 text-white' : ($tr->lunch_start_time ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-500')) }}">3</div>
                                <div class="text-[11px] font-bold text-slate-800">Lunch (1h)</div>
                                <div class="text-[10px] {{ $tr->lunch_exceeded ? 'text-red-600 font-bold' : 'text-slate-500' }}">{{ $tr->lunch_duration_minutes ? $tr->lunch_duration_minutes.'m' : 'Pending' }}</div>
                            </div>

                            <!-- Step 4 -->
                            <div class="space-y-1">
                                <div class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center mx-auto {{ $tr->slot3_is_flagged ? 'bg-red-600 text-white' : ($tr->slot3_end_time ? 'bg-emerald-600 text-white' : ($tr->slot3_start_time ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-500')) }}">4</div>
                                <div class="text-[11px] font-bold text-slate-800">3rd Slot</div>
                                <div class="text-[10px] {{ $tr->slot3_is_flagged ? 'text-red-600 font-bold' : 'text-slate-500' }}">{{ $tr->slot3_end_time ? $tr->slot3_end_time?->format('g:i A') : 'Pending' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clock Sessions List -->
                <div class="space-y-2.5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Clock Sessions & Breaks</h3>

                    <div class="border border-slate-200/70 rounded-xl overflow-hidden divide-y divide-slate-100">
                        @forelse($logs as $idx => $log)
                            <div class="p-3 bg-white flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800">Session #{{ $idx + 1 }}: {{ $log->clock_in_time?->format('g:i A') }} → {{ $log->clock_out_time ? $log->clock_out_time?->format('g:i A') : 'Active' }}</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[11px]">
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
                        class="px-4 py-2 text-xs font-bold rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
