<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Daily Slot Roadmap
                    </span>
                    <span class="text-xs text-slate-500 font-medium">{{ now()->format('l, F j, Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Welcome, {{ auth()->user()->name }} 👋</h1>
                <p class="text-slate-600 mt-1 text-sm">Follow your daily shift timeline: Slot 1 Clock-In → 2-Hr Check-In (after 1.5h) → Lunch Break (1h) → 3rd Slot.</p>
            </div>

            <div>
                <div class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Shift Status</span>
                    @if(!$tracking)
                        <span class="text-xs font-bold text-slate-600">Not Clocked In</span>
                    @elseif($tracking->slot3_end_time)
                        <span class="text-xs font-bold text-emerald-600">Shift Finished</span>
                    @elseif($tracking->lunch_start_time && !$tracking->lunch_end_time)
                        <span class="text-xs font-bold text-amber-600">On Lunch Break (1h)</span>
                    @elseif($tracking->slot3_start_time)
                        <span class="text-xs font-bold text-indigo-600">In 3rd Working Slot</span>
                    @elseif($tracking->slot1_2hr_checkin_time)
                        <span class="text-xs font-bold text-blue-600">Completed 2-Hr Slot</span>
                    @else
                        <span class="text-xs font-bold text-blue-600">Slot 1 Active</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session()->has('attendance_status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('attendance_status') }}</span>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Horizontal Stepper / Progress Bar -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-6">
        <h2 class="text-base font-bold text-slate-900">Shift Progress Timeline</h2>

        <!-- Horizontal Stepper Bar -->
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Connecting Line behind circles -->
            <div class="hidden md:block absolute top-5 left-8 right-8 h-1 bg-slate-200 -z-0"></div>

            <!-- Step 1: Initial Clock-In -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all shadow-xs {{ $tracking ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' }}">
                    {{ $tracking ? '✓' : '1' }}
                </div>
                <span class="text-xs font-bold text-slate-900 mt-2">1. Shift Clock-In</span>
                <span class="text-[11px] text-slate-500">
                    {{ $tracking && $tracking->slot1_checkin_time ? $tracking->slot1_checkin_time?->format('g:i A') : 'Automatic' }}
                </span>
            </div>

            <!-- Step 2: Slot 2 Check-In (Disabled until 1.5 hrs) -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                @if(!$tracking || !$tracking->slot1_checkin_time)
                    <!-- Disabled (Not Clocked In) -->
                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-sm cursor-not-allowed">
                        2
                    </div>
                    <span class="text-xs font-semibold text-slate-400 mt-2">2. Slot 2 Check-In</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-400 font-medium">Disabled</span>

                @elseif(!$tracking->slot2_checkin_time)
                    @if($is2HrCheckinEligible)
                        <!-- Enabled for Check-In -->
                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-blue-100 animate-bounce">
                            2
                        </div>
                        <span class="text-xs font-bold text-blue-700 mt-2">2. Slot 2 Check-In</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">Ready Now!</span>
                    @else
                        <!-- Disabled Timer Countdown -->
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 border border-slate-300 flex items-center justify-center font-bold text-sm cursor-not-allowed">
                            2
                        </div>
                        <span class="text-xs font-semibold text-slate-500 mt-2">2. Slot 2 Check-In</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">
                            Available in ~{{ $minutesRemainingFor2HrCheckin }}m
                        </span>
                    @endif

                @else
                    <!-- Completed -->
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $tracking->slot2_is_flagged ? 'bg-red-600 text-white ring-4 ring-red-100' : 'bg-emerald-600 text-white ring-4 ring-emerald-100' }}">
                        {{ $tracking->slot2_is_flagged ? '⚠️' : '✓' }}
                    </div>
                    <span class="text-xs font-bold text-slate-900 mt-2">2. Slot 2 Check-In</span>
                    <span class="text-[11px] {{ $tracking->slot2_is_flagged ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                        {{ $tracking->slot2_checkin_time?->format('g:i A') ?? 'Done' }}
                    </span>
                @endif
            </div>

            <!-- Step 3: Lunch Break (1 Hour Max) -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                @if(!$tracking || !$tracking->slot2_checkin_time)
                    <!-- Disabled -->
                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-sm cursor-not-allowed">
                        3
                    </div>
                    <span class="text-xs font-semibold text-slate-400 mt-2">3. Lunch Break (1h)</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-400 font-medium">Disabled</span>

                @elseif(!$tracking->lunch_start_time)
                    <!-- Ready to start lunch -->
                    <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm ring-4 ring-amber-100">
                        3
                    </div>
                    <span class="text-xs font-bold text-amber-800 mt-2">3. Lunch Break (1h)</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-amber-100 text-amber-900 font-bold">Ready</span>

                @elseif(!$tracking->lunch_end_time)
                    <!-- Lunch active -->
                    <div class="w-10 h-10 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-amber-200 animate-pulse">
                        3
                    </div>
                    <span class="text-xs font-bold text-amber-900 mt-2">3. Lunch In Progress</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-amber-200 text-amber-900 font-bold">Started {{ $tracking->lunch_start_time?->format('g:i A') }}</span>

                @else
                    <!-- Lunch completed -->
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $tracking->lunch_exceeded ? 'bg-red-600 text-white ring-4 ring-red-100' : 'bg-emerald-600 text-white ring-4 ring-emerald-100' }}">
                        {{ $tracking->lunch_exceeded ? '⚠️' : '✓' }}
                    </div>
                    <span class="text-xs font-bold text-slate-900 mt-2">3. Lunch Break (1h)</span>
                    <span class="text-[11px] {{ $tracking->lunch_exceeded ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                        {{ $tracking->lunch_duration_minutes }} mins {{ $tracking->lunch_exceeded ? '(+'.$tracking->lunch_exceeded_minutes.'m Exceeded)' : '' }}
                    </span>
                @endif
            </div>

            <!-- Step 4: 3rd Slot & Clock Out -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                @if(!$tracking || !$tracking->lunch_end_time)
                    <!-- Disabled -->
                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-sm cursor-not-allowed">
                        4
                    </div>
                    <span class="text-xs font-semibold text-slate-400 mt-2">4. 3rd Slot & Clock Out</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-400 font-medium">Disabled</span>

                @elseif(!$tracking->slot3_start_time)
                    <!-- Ready to start 3rd slot -->
                    <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-sm ring-4 ring-indigo-100 animate-bounce">
                        4
                    </div>
                    <span class="text-xs font-bold text-indigo-800 mt-2">4. 3rd Slot</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-100 text-indigo-900 font-bold">Ready</span>

                @elseif(!$tracking->slot3_end_time)
                    <!-- In 3rd Slot -->
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-indigo-100 animate-pulse">
                        4
                    </div>
                    <span class="text-xs font-bold text-indigo-800 mt-2">4. 3rd Slot Active</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-100 text-indigo-900 font-bold">Started {{ $tracking->slot3_start_time?->format('g:i A') }}</span>

                @else
                    <!-- Completed -->
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $tracking->slot3_is_flagged ? 'bg-red-600 text-white ring-4 ring-red-100' : 'bg-emerald-600 text-white ring-4 ring-emerald-100' }}">
                        {{ $tracking->slot3_is_flagged ? '⚠️' : '✓' }}
                    </div>
                    <span class="text-xs font-bold text-slate-900 mt-2">4. 3rd Slot Completed</span>
                    <span class="text-[11px] {{ $tracking->slot3_is_flagged ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                        Ended {{ $tracking->slot3_end_time?->format('g:i A') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Step Action Cards -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-6">
        <h2 class="text-base font-bold text-slate-900">Current Action Step</h2>

        <!-- Step 1 Form: Initial Clock-In -->
        @if(!$tracking || !$tracking->slot1_checkin_time)
            <div class="p-6 rounded-xl border border-blue-200 bg-blue-50/40 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-700 uppercase tracking-wide">Step 1</span>
                    <h3 class="text-lg font-bold text-slate-900 mt-2">Daily Shift Clock-In</h3>
                    <p class="text-slate-600 text-xs mt-0.5">Click below to start your shift. Your Slot 2 check-in will unlock after 1.5 hours.</p>
                </div>
                <button 
                    wire:click="clockIn" 
                    wire:loading.attr="disabled"
                    class="px-6 py-3 rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition-all shadow-sm flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <svg wire:loading.remove wire:target="clockIn" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Start Daily Shift Clock-In</span>
                </button>
            </div>

        <!-- Step 2 Form: Slot 2 Check-In -->
        @elseif(!$tracking->slot2_checkin_time)
            <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-200 text-slate-700 uppercase tracking-wide">Step 2</span>
                    <h3 class="text-base font-bold text-slate-900 mt-2">Slot 2 Check-In</h3>
                    <p class="text-xs text-slate-600">
                        Shift started at <strong class="text-slate-900">{{ $tracking->slot1_checkin_time?->format('g:i A') }}</strong>. 
                        Note: Check-in is allowed between 1.5 hrs and 2.5 hrs. Marking after 2.5 hrs (&gt;30m past 2h) will flag in <strong class="text-red-600">RED</strong>.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if(!$is2HrCheckinEligible)
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                            🔒 Disabled for ~{{ $minutesRemainingFor2HrCheckin }} mins
                        </span>
                    @endif

                    <button 
                        wire:click="save2HrCheckin" 
                        wire:loading.attr="disabled"
                        @if(!$is2HrCheckinEligible) disabled class="px-5 py-2.5 rounded-lg text-xs font-bold text-slate-400 bg-slate-200 cursor-not-allowed" @else class="px-5 py-2.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition-all shadow-xs flex items-center gap-2 cursor-pointer" @endif
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Confirm Slot 2 Check-In</span>
                    </button>
                </div>
            </div>

        <!-- Step 3 Form: Lunch Controls -->
        @elseif(!$tracking->lunch_start_time)
            <div class="p-6 rounded-xl border border-amber-200 bg-amber-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wide">Step 3</span>
                    <h3 class="text-base font-bold text-slate-900 mt-2">Start Lunch Break</h3>
                    <p class="text-xs text-slate-600">Lunch duration is strictly <strong>1 hour</strong>. Exceeding 1 hour will flag in <strong class="text-red-600">RED</strong> for Admin review.</p>
                </div>
                <button 
                    wire:click="startLunch" 
                    wire:loading.attr="disabled"
                    class="px-6 py-3 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Start Lunch Break (1 Hour)</span>
                </button>
            </div>

        @elseif(!$tracking->lunch_end_time)
            <div class="p-6 rounded-xl border border-amber-300 bg-amber-100/50 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-200 text-amber-900 uppercase tracking-wide">Step 3</span>
                        <h3 class="text-base font-bold text-slate-900 mt-2">Lunch Break In Progress</h3>
                        <p class="text-xs text-slate-700">Lunch started at <strong class="text-slate-900">{{ $tracking->lunch_start_time?->format('g:i A') }}</strong>. Click below when returning to start 3rd slot.</p>
                    </div>
                    <button 
                        wire:click="endLunchAndStartSlot3" 
                        wire:loading.attr="disabled"
                        class="px-6 py-3 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                        </svg>
                        <span>Return From Lunch & Start 3rd Slot</span>
                    </button>
                </div>
            </div>

        <!-- Step 4 Form: Start 3rd Slot (If lunch ended but slot 3 not started) -->
        @elseif(!$tracking->slot3_start_time)
            <div class="p-6 rounded-xl border border-indigo-200 bg-indigo-50/40 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wide">Step 4</span>
                    <h3 class="text-base font-bold text-slate-900 mt-2">Start 3rd Working Slot</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Returned from lunch at <strong class="text-slate-900">{{ $tracking->lunch_end_time?->format('g:i A') }}</strong>. Click below to start your 3rd slot.</p>
                </div>

                <button 
                    wire:click="startSlot3" 
                    wire:loading.attr="disabled"
                    class="px-6 py-3 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    </svg>
                    <span>Start 3rd Working Slot</span>
                </button>
            </div>

        <!-- Step 4 Form: 3rd Slot Active & Clock Out -->
        @elseif(!$tracking->slot3_end_time)
            <div class="p-6 rounded-xl border border-indigo-200 bg-indigo-50/40 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wide">Step 4</span>
                    <h3 class="text-base font-bold text-slate-900 mt-2">3rd Working Slot & Clock Out</h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        3rd slot started at <strong class="text-slate-900">{{ $tracking->slot3_start_time?->format('g:i A') }}</strong>. 
                        Note: Completing 3rd slot <strong>&gt;30 mins early or exceeded</strong> will flag your entry in <strong class="text-red-600">RED</strong> for Admin review.
                    </p>
                </div>

                <button 
                    wire:click="saveSlot3AndClockOut" 
                    wire:loading.attr="disabled"
                    class="px-6 py-3 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Complete 3rd Slot & Clock Out</span>
                </button>
            </div>

        @else
            <!-- Day Completed Banner -->
            <div class="p-6 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">✓</div>
                    <div>
                        <h3 class="text-base font-bold text-emerald-950">Shift Activity Tracking Completed!</h3>
                        <p class="text-xs text-emerald-800">You have completed Slot 1, 2-Hr Check-In, Lunch Break, and 3rd Slot for today.</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-white px-3 py-1.5 rounded-lg border border-emerald-200">
                    Shift Ended: {{ $tracking->slot3_end_time?->format('g:i A') }}
                </span>
            </div>
        @endif
    </div>

    <!-- Summary Card -->
    @if($tracking)
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900">Today's Shift Summary</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Slot 2 Details Card -->
                <div class="p-4 rounded-lg border space-y-2 {{ $tracking->slot2_is_flagged ? 'bg-red-50/70 border-red-200' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-bold uppercase tracking-wide {{ $tracking->slot2_is_flagged ? 'text-red-800' : 'text-slate-800' }}">Slot 2 Check-In</span>
                        @if($tracking->slot2_is_flagged)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-600 text-white uppercase">
                                Exceeded (+{{ $tracking->slot2_deviation_minutes }}m)
                            </span>
                        @elseif($tracking->slot2_checkin_time)
                            <span class="text-emerald-700 font-bold">✓ {{ $tracking->slot2_checkin_time?->format('g:i A') }} (On Schedule)</span>
                        @else
                            <span class="text-slate-500">Pending</span>
                        @endif
                    </div>
                </div>

                <!-- 3rd Slot Details Card -->
                <div class="p-4 rounded-lg border space-y-2 {{ $tracking->slot3_is_flagged ? 'bg-red-50/70 border-red-200' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-bold uppercase tracking-wide {{ $tracking->slot3_is_flagged ? 'text-red-800' : 'text-slate-800' }}">3rd Slot</span>
                        @if($tracking->slot3_is_flagged)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-600 text-white uppercase">
                                Exceeded (+{{ $tracking->slot3_deviation_minutes }}m)
                            </span>
                        @elseif($tracking->slot3_end_time)
                            <span class="text-emerald-700 font-bold">✓ {{ $tracking->slot3_end_time?->format('g:i A') }} (On Schedule)</span>
                        @else
                            <span class="text-slate-500">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
