<aside 
    class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 md:translate-x-0 fixed md:static inset-y-0 left-0 z-40 shadow-xs"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div>
        <!-- Brand Header with stacked Logo and Employee Portal in col -->
        <div class="py-4 flex items-center justify-between px-5 border-b border-slate-200">
            <div class="flex flex-col items-start gap-1">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="h-9 w-auto rounded-md object-contain shrink-0">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Employee Portal</span>
            </div>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 p-1">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        <!-- Attendance / Clock In Widget Card -->
        <div class="p-3.5">
            <div class="bg-slate-50/80 border border-slate-200/90 rounded-md p-4 space-y-3">
                <!-- Date & Status Indicator Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                        <i class="ri-calendar-line text-slate-400"></i>
                        <span>{{ $todayDateFormatted }}</span>
                    </div>

                    @if($activeLog)
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Working
                        </span>
                    @elseif($todayAttendance && $todayLogs->isNotEmpty())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            On Break
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-200/70 text-slate-600 border border-slate-300/60">
                            Not Clocked In
                        </span>
                    @endif
                </div>

                @if(isset($todayHoliday) && $todayHoliday)
                    <div class="px-2.5 py-1.5 rounded-md bg-purple-50 border border-purple-200 text-purple-800 text-[11px] font-semibold flex items-center justify-between gap-1.5">
                        <span class="flex items-center gap-1.5 truncate">
                            <i class="ri-calendar-event-line text-purple-600 shrink-0"></i>
                            <span class="truncate">Holiday: {{ $todayHoliday->name }}</span>
                        </span>
                        <span class="text-[9px] uppercase font-bold text-purple-700 bg-purple-100 px-1 py-0.5 rounded shrink-0">Work Optional</span>
                    </div>
                @endif

                <!-- Clock In Details -->
                @if($activeLog && $activeLog->clock_in_time)
                    <div class="bg-white rounded-md p-2.5 border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Clock In Time:</span>
                        <span class="font-semibold text-slate-900 flex items-center gap-1">
                            <i class="ri-time-line text-slate-500"></i>
                            {{ $activeLog->clock_in_time?->format('g:i A') }}
                        </span>
                    </div>
                @endif

                <!-- Flash Message Notification -->
                @if(session()->has('attendance_status'))
                    <p class="text-[11px] font-medium text-emerald-800 bg-emerald-50 p-2 rounded-md border border-emerald-200 text-center leading-tight">
                        {{ session('attendance_status') }}
                    </p>
                @endif

                <!-- Dynamic Clock Action Button -->
                <div>
                    @if(!$activeLog)
                        <button 
                            wire:click="clockIn" 
                            wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-xs cursor-pointer disabled:opacity-50"
                        >
                            <i wire:loading.remove wire:target="clockIn" class="ri-login-box-line text-sm"></i>
                            <i wire:loading wire:target="clockIn" class="ri-loader-4-line animate-spin text-sm"></i>
                            <span>Clock In Now</span>
                        </button>
                    @else
                        <div
                            wire:key="clock-out-{{ $activeLog->id }}"
                            x-data="{
                                open: false,
                                completedMinutes: {{ (int) $completedWorkMinutes }},
                                requiredMinutes: {{ (int) $requiredWorkMinutes }},
                                activeSince: {{ $activeLog->clock_in_time ? $activeLog->clock_in_time->getTimestamp() * 1000 : 'null' }},
                                workedMinutes: 0,
                                calc() {
                                    const active = this.activeSince ? Math.max(0, Math.floor((Date.now() - this.activeSince) / 60000)) : 0;
                                    this.workedMinutes = this.completedMinutes + active;
                                },
                                fmt(m) {
                                    const h = Math.floor(m / 60), r = m % 60;
                                    return h + 'h ' + String(r).padStart(2, '0') + 'm';
                                },
                                get isShort() { return this.workedMinutes < this.requiredMinutes; },
                                ask() { this.calc(); this.open = true; },
                                confirm() { this.open = false; $wire.clockOut(); },
                            }"
                            @keydown.escape.window="open = false"
                        >
                            <button
                                type="button"
                                @click="ask()"
                                wire:loading.attr="disabled"
                                wire:target="clockOut"
                                class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-md text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 transition-all shadow-xs cursor-pointer disabled:opacity-50"
                            >
                                <i wire:loading.remove wire:target="clockOut" class="ri-logout-box-r-line text-sm"></i>
                                <i wire:loading wire:target="clockOut" class="ri-loader-4-line animate-spin text-sm"></i>
                                <span>Clock Out Now</span>
                            </button>

                            <!-- Clock Out Confirmation Modal (teleported so the sidebar transform doesn't clip it) -->
                            <template x-teleport="body">
                                <div
                                    x-show="open"
                                    x-transition.opacity
                                    x-cloak
                                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50"
                                    @click.self="open = false"
                                >
                                    <div class="w-full max-w-sm bg-white rounded-lg border border-slate-200 shadow-xl p-5 space-y-4">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                                :class="isShort ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-700'"
                                            >
                                                <i class="text-xl" :class="isShort ? 'ri-error-warning-line' : 'ri-logout-box-r-line'"></i>
                                            </div>
                                            <div class="space-y-1">
                                                <h3 class="text-sm font-semibold text-slate-900">Confirm Clock Out</h3>
                                                <p class="text-xs text-slate-600">Are you sure you want to clock out?</p>
                                            </div>
                                        </div>

                                        <template x-if="isShort">
                                            <div class="text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-md p-3 space-y-1">
                                                <p class="font-semibold">You are clocking out before completing 8 hours.</p>
                                                <p>
                                                    Worked today: <span class="font-semibold" x-text="fmt(workedMinutes)"></span>
                                                    &middot; Remaining: <span class="font-semibold" x-text="fmt(requiredMinutes - workedMinutes)"></span>
                                                </p>
                                            </div>
                                        </template>

                                        <template x-if="!isShort">
                                            <div class="text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md p-3">
                                                You have completed your 8 hours today
                                                (<span class="font-semibold" x-text="fmt(workedMinutes)"></span> worked).
                                            </div>
                                        </template>

                                        <div class="flex items-center justify-end gap-2 pt-1">
                                            <button
                                                type="button"
                                                @click="open = false"
                                                class="px-3.5 py-2 rounded-md text-xs font-medium text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 cursor-pointer"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="button"
                                                @click="confirm()"
                                                class="px-3.5 py-2 rounded-md text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 cursor-pointer"
                                            >
                                                Yes, Clock Out
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="px-3 py-2 space-y-1">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('employee.dashboard') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-dashboard-3-line text-base {{ request()->routeIs('employee.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('employee.daily-task-report-list') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('employee.daily-task-report*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-file-paper-2-line text-base {{ request()->routeIs('employee.daily-task-report*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Daily Task Reports</span>
            </a>

            <a href="{{ route('employee.attendance-logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('employee.attendance-logs') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-time-line text-base {{ request()->routeIs('employee.attendance-logs') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Working Hours & Slots</span>
            </a>

            <a href="{{ route('employee.leave-requests') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('employee.leave-requests') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-calendar-check-line text-base {{ request()->routeIs('employee.leave-requests') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Leave Requests</span>
            </a>
        </nav>
    </div>

    <!-- User Profile Footer -->
    <div class="p-3.5 border-t border-slate-200 space-y-3 bg-slate-50/50">
        <div class="flex items-center gap-3 px-1">
            <div class="w-8 h-8 rounded-md bg-white border border-slate-200/80 shadow-xs flex items-center justify-center font-bold text-slate-800 text-xs shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)) }}
            </div>
            <div class="overflow-hidden min-w-0">
                <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->name ?? 'Employee' }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'employee@example.com' }}</p>
            </div>
        </div>

        <button 
            wire:click="logout" 
            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-md text-xs font-medium text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition-all cursor-pointer shadow-2xs"
        >
            <i class="ri-logout-box-r-line text-slate-400"></i>
            <span>Sign Out</span>
        </button>
    </div>
</aside>