<div class="space-y-6" wire:poll.10s x-data="{ showConfirmModal: false }">
    <!-- Header Banner -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-slate-900 animate-pulse"></span>
                        Daily Slot Roadmap
                    </span>
                    <span class="text-xs text-slate-500 font-medium">{{ now()->format('l, F j, Y') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome, {{ auth()->user()->name }} 👋</h1>
                <p class="text-slate-500 mt-1 text-xs sm:text-sm">Follow your daily shift timeline: Slot 1 Clock-In → 2-Hr Check-In (after 2h) → Lunch Break (1h) → 3rd Slot → 4th Slot & Clock Out (after 90m).</p>
            </div>

            <div>
                <div class="bg-slate-50/80 border border-slate-200/90 rounded-md px-4 py-2.5 text-right space-y-1">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Shift Status</span>
                    @if(!$tracking)
                        <span class="text-xs font-semibold text-slate-600 block">Not Clocked In</span>
                    @elseif($tracking->slot4_checkin_time)
                        <span class="text-xs font-semibold text-emerald-600 block">Shift Finished</span>
                    @elseif($tracking->slot3_start_time)
                        <span class="text-xs font-semibold text-indigo-600 block">In 3rd Working Slot</span>
                    @elseif($tracking->lunch_start_time && !$tracking->lunch_end_time)
                        <span class="text-xs font-semibold text-amber-600 block">On Lunch Break (1h)</span>
                    @elseif($tracking->slot2_checkin_time)
                        <span class="text-xs font-semibold text-blue-600 block">Completed 2-Hr Slot</span>
                    @else
                        <span class="text-xs font-semibold text-blue-600 block">Slot 1 Active</span>
                    @endif

                    <a href="{{ route('employee.attendance-logs') }}" class="text-[11px] font-medium text-slate-600 hover:text-slate-900 underline inline-flex items-center gap-1 mt-1 transition-colors">
                        <i class="ri-history-line"></i> View Past Slot Records
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session()->has('attendance_status'))
        <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-3 shadow-2xs">
            <i class="ri-checkbox-circle-fill text-emerald-600 text-base shrink-0"></i>
            <span>{{ session('attendance_status') }}</span>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 rounded-md bg-red-50 border border-red-200 text-red-800 text-xs font-medium flex items-center gap-3 shadow-2xs">
            <i class="ri-error-warning-fill text-red-600 text-base shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Horizontal Stepper / Progress Bar -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="ri-route-line text-slate-500"></i>
                Shift Progress Timeline
            </h2>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                5-Step Roadmap
            </span>
        </div>

        <!-- Horizontal Stepper Progress Bar Track -->
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-4 py-2">
            <!-- Connecting Circular Progress Bar Line behind circles -->
            <div class="hidden md:block absolute top-7 left-10 right-10 h-2 bg-slate-100/90 border border-slate-200/80 rounded-full -z-0 overflow-hidden">
                <div 
                    class="h-full bg-slate-900 rounded-full transition-all duration-500" 
                    style="width: {{ !$tracking ? '0%' : ($tracking->slot4_checkin_time ? '100%' : ($tracking->slot3_start_time ? '75%' : ($tracking->lunch_start_time ? '50%' : ($tracking->slot2_checkin_time ? '25%' : '10%')))) }};"
                ></div>
            </div>

            <!-- Step 1: Initial Clock-In -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all shadow-xs {{ $tracking ? 'bg-slate-900 text-white ring-4 ring-slate-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                    @if($tracking) <i class="ri-check-line text-base"></i> @else 1 @endif
                </div>
                <span class="text-xs font-semibold text-slate-900 mt-2">1. Shift Clock-In</span>
                <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium mt-0.5">
                    {{ $tracking && $tracking->slot1_checkin_time ? $tracking->slot1_checkin_time?->format('g:i A') : 'Automatic' }}
                </span>
            </div>

            <!-- Step 2: Slot 2 Check-In -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                @if(!$tracking || !$tracking->slot1_checkin_time)
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-medium text-sm cursor-not-allowed">
                        2
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2">2. Slot 2 Check-In</span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-400 font-medium mt-0.5">Disabled</span>

                @elseif(!$tracking->slot2_checkin_time)
                    @if($is2HrCheckinEligible)
                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-blue-100 animate-bounce shadow-md">
                            2
                        </div>
                        <span class="text-xs font-semibold text-slate-900 mt-2">2. Slot 2 Check-In</span>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-900 font-semibold mt-0.5">Ready Now!</span>
                    @else
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-medium text-sm cursor-not-allowed">
                            2
                        </div>
                        <span class="text-xs font-medium text-slate-500 mt-2">2. Slot 2 Check-In</span>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium mt-0.5">
                            Available in ~{{ $minutesRemainingFor2HrCheckin }}m
                        </span>
                    @endif

                @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm {{ $tracking->slot2_is_flagged ? 'bg-red-600 text-white ring-4 ring-red-100' : 'bg-slate-900 text-white ring-4 ring-slate-100' }}">
                        @if($tracking->slot2_is_flagged) <i class="ri-error-warning-line text-base"></i> @else <i class="ri-check-line text-base"></i> @endif
                    </div>
                    <span class="text-xs font-semibold text-slate-900 mt-2">2. Slot 2 Check-In</span>
                    <span class="text-[11px] px-2.5 py-0.5 rounded-full {{ $tracking->slot2_is_flagged ? 'bg-red-50 text-red-700 font-semibold' : 'bg-slate-100 text-slate-600' }} mt-0.5">
                        {{ $tracking->slot2_checkin_time?->format('g:i A') ?? 'Done' }}
                    </span>
                @endif
            </div>

            <!-- Step 3: Lunch Break -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                @if(!$tracking || !$tracking->slot2_checkin_time)
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-medium text-sm cursor-not-allowed">
                        3
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2">3. Lunch Break (1h)</span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-400 font-medium mt-0.5">Disabled</span>

                @elseif(!$tracking->lunch_start_time)
                    <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm ring-4 ring-amber-100 shadow-md">
                        3
                    </div>
                    <span class="text-xs font-semibold text-amber-900 mt-2">3. Lunch Break (1h)</span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-semibold mt-0.5">Ready</span>

                @elseif(!$tracking->lunch_end_time)
                    <div class="w-10 h-10 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-amber-100 animate-pulse shadow-md">
                        3
                    </div>
                    <span class="text-xs font-semibold text-amber-900 mt-2">3. Lunch In Progress</span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-semibold mt-0.5">Started {{ $tracking->lunch_start_time?->format('g:i A') }}</span>

                @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm {{ $tracking->lunch_exceeded ? 'bg-red-600 text-white ring-4 ring-red-100' : 'bg-slate-900 text-white ring-4 ring-slate-100' }}">
                        @if($tracking->lunch_exceeded) <i class="ri-error-warning-line text-base"></i> @else <i class="ri-check-line text-base"></i> @endif
                    </div>
                    <span class="text-xs font-semibold text-slate-900 mt-2">3. Lunch Break (1h)</span>
                    <span class="text-[11px] px-2.5 py-0.5 rounded-full {{ $tracking->lunch_exceeded ? 'bg-red-50 text-red-700 font-semibold' : 'bg-slate-100 text-slate-600' }} mt-0.5">
                        {{ $tracking->lunch_duration_minutes }} mins {{ $tracking->lunch_exceeded ? '(+'.$tracking->lunch_exceeded_minutes.'m Exceeded)' : '' }}
                    </span>
                @endif
            </div>

            <!-- Step 4: 3rd Slot Start -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                @if(!$tracking || !$tracking->lunch_end_time)
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-medium text-sm cursor-not-allowed">
                        4
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2">4. 3rd Slot</span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-400 font-medium mt-0.5">Disabled</span>

                @elseif(!$tracking->slot3_start_time)
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-indigo-100 animate-bounce shadow-md">
                        4
                    </div>
                    <span class="text-xs font-semibold text-slate-900 mt-2">4. 3rd Slot</span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-900 font-semibold mt-0.5">Ready</span>

                @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm bg-slate-900 text-white ring-4 ring-slate-100">
                        <i class="ri-check-line text-base"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-900 mt-2">4. 3rd Slot Started</span>
                    <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 mt-0.5">
                        {{ $tracking->slot3_start_time?->format('g:i A') }}
                    </span>
                @endif
            </div>

            <!-- Step 5: 4th Slot & Clock Out -->
            <div class="flex-1 flex flex-col items-center text-center z-10 w-full md:w-auto">
                @if(!$tracking || !$tracking->slot3_start_time)
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-medium text-sm cursor-not-allowed">
                        5
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2">5. 4th Slot & Clock Out</span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-400 font-medium mt-0.5">Disabled</span>

                @elseif(!$tracking->slot4_checkin_time)
                    @if($is4thSlotEligible)
                        <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-emerald-100 animate-bounce shadow-md">
                            5
                        </div>
                        <span class="text-xs font-semibold text-slate-900 mt-2">5. 4th Slot Check-In</span>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 font-semibold mt-0.5">Ready Now!</span>
                    @else
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center font-medium text-sm cursor-not-allowed">
                            5
                        </div>
                        <span class="text-xs font-medium text-slate-500 mt-2">5. 4th Slot (90m)</span>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium mt-0.5">
                            Available in ~{{ $minutesRemainingFor4thSlot }}m
                        </span>
                    @endif

                @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm {{ $tracking->slot4_is_flagged ? 'bg-red-600 text-white ring-4 ring-red-100' : 'bg-emerald-600 text-white ring-4 ring-emerald-100' }}">
                        @if($tracking->slot4_is_flagged) <i class="ri-error-warning-line text-base"></i> @else <i class="ri-check-line text-base"></i> @endif
                    </div>
                    <span class="text-xs font-semibold text-slate-900 mt-2">5. 4th Slot & Clock Out</span>
                    <span class="text-[11px] px-2.5 py-0.5 rounded-full {{ $tracking->slot4_is_flagged ? 'bg-red-50 text-red-700 font-semibold' : 'bg-emerald-50 text-emerald-800 font-semibold' }} mt-0.5">
                        Ended {{ $tracking->slot4_checkin_time?->format('g:i A') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Step Action Cards -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-6">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Current Action Step</h2>

        <!-- Step 1 Form -->
        @if(!$tracking || !$tracking->slot1_checkin_time)
            <div class="p-5 rounded-md border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 text-slate-700 uppercase tracking-wide">Step 1</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1.5">Daily Shift Clock-In</h3>
                    <p class="text-slate-500 text-xs mt-0.5">Click below to start your shift. Your Slot 2 check-in will unlock after 2 hours (120 min).</p>
                </div>
                <button 
                    wire:click="clockIn" 
                    wire:loading.attr="disabled"
                    class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <i wire:loading.remove wire:target="clockIn" class="ri-time-line text-sm"></i>
                    <i wire:loading wire:target="clockIn" class="ri-loader-4-line animate-spin text-sm"></i>
                    <span>Start Daily Shift Clock-In</span>
                </button>
            </div>

        <!-- Step 2 Form -->
        @elseif(!$tracking->slot2_checkin_time)
            <div class="p-5 rounded-md border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 text-slate-700 uppercase tracking-wide">Step 2</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1.5">Slot 2 Check-In</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Shift started at <strong class="text-slate-900">{{ $tracking->slot1_checkin_time?->format('g:i A') }}</strong>. 
                        Note: Check-in is unlocked after 2 hours (120 min). Exceeding by 30 mins (>150 min total) will flag entry in RED.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if(!$is2HrCheckinEligible)
                        <span class="px-3 py-1.5 rounded-md text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                            <i class="ri-lock-line text-xs"></i> Disabled for ~{{ $minutesRemainingFor2HrCheckin }} mins
                        </span>
                    @endif

                    <button 
                        wire:click="save2HrCheckin" 
                        wire:loading.attr="disabled"
                        @if(!$is2HrCheckinEligible) disabled class="px-4 py-2.5 rounded-md text-xs font-semibold text-slate-400 bg-slate-200 cursor-not-allowed" @else class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-xs flex items-center gap-2 cursor-pointer" @endif
                    >
                        <i wire:loading.remove wire:target="save2HrCheckin" class="ri-checkbox-circle-line text-sm"></i>
                        <i wire:loading wire:target="save2HrCheckin" class="ri-loader-4-line animate-spin text-sm"></i>
                        <span>Confirm Slot 2 Check-In</span>
                    </button>
                </div>
            </div>

        <!-- Step 3 Form -->
        @elseif(!$tracking->lunch_start_time)
            <div class="p-5 rounded-md border border-amber-200/80 bg-amber-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 uppercase tracking-wide">Step 3</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1.5">Start Lunch Break</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Lunch duration is strictly <strong>1 hour</strong>. Exceeding 1 hour will be flagged for Admin review.</p>
                </div>
                <button 
                    wire:click="startLunch" 
                    wire:loading.attr="disabled"
                    class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <i wire:loading.remove wire:target="startLunch" class="ri-cup-line text-sm"></i>
                    <i wire:loading wire:target="startLunch" class="ri-loader-4-line animate-spin text-sm"></i>
                    <span>Start Lunch Break (1 Hour)</span>
                </button>
            </div>

        @elseif(!$tracking->lunch_end_time)
            <div class="p-5 rounded-md border border-amber-200 bg-amber-50/70 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-amber-200 text-amber-900 uppercase tracking-wide">Step 3</span>
                        <h3 class="text-base font-bold text-slate-900 mt-1.5">Lunch Break In Progress</h3>
                        <p class="text-xs text-slate-700 mt-0.5">Lunch started at <strong class="text-slate-900">{{ $tracking->lunch_start_time?->format('g:i A') }}</strong>. Click below when returning to start 3rd slot.</p>
                    </div>
                    <button 
                        wire:click="endLunchAndStartSlot3" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                    >
                        <i wire:loading.remove wire:target="endLunchAndStartSlot3" class="ri-arrow-right-line text-sm"></i>
                        <i wire:loading wire:target="endLunchAndStartSlot3" class="ri-loader-4-line animate-spin text-sm"></i>
                        <span>Return From Lunch & Start 3rd Slot</span>
                    </button>
                </div>
            </div>

        <!-- Step 4 Form: Start 3rd Slot -->
        @elseif(!$tracking->slot3_start_time)
            <div class="p-5 rounded-md border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 text-slate-700 uppercase tracking-wide">Step 4</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1.5">Start 3rd Working Slot</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Returned from lunch at <strong class="text-slate-900">{{ $tracking->lunch_end_time?->format('g:i A') }}</strong>. Click below to start your 3rd slot.</p>
                </div>

                <button 
                    wire:click="startSlot3" 
                    wire:loading.attr="disabled"
                    class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-xs flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <i wire:loading.remove wire:target="startSlot3" class="ri-play-circle-line text-sm"></i>
                    <i wire:loading wire:target="startSlot3" class="ri-loader-4-line animate-spin text-sm"></i>
                    <span>Start 3rd Working Slot</span>
                </button>
            </div>

        <!-- Step 5 Form: 4th Slot & Clock Out -->
        @elseif(!$tracking->slot4_checkin_time)
            <div class="p-5 rounded-md border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-100 text-indigo-800 uppercase tracking-wide">Step 5</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1.5">4th Slot Check-In & Shift Clock-Out</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        3rd slot started at <strong class="text-slate-900">{{ $tracking->slot3_start_time?->format('g:i A') }}</strong>. 
                        Note: 4th slot check-in opens after 90 minutes. Exceeding by 30 mins (&gt;120m total) will flag entry in RED.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if(!$is4thSlotEligible)
                        <span class="px-3 py-1.5 rounded-md text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                            <i class="ri-lock-line text-xs"></i> Disabled for ~{{ $minutesRemainingFor4thSlot }} mins
                        </span>
                    @endif

                    <button 
                        type="button"
                        @if(!$is4thSlotEligible) disabled class="px-4 py-2.5 rounded-md text-xs font-semibold text-slate-400 bg-slate-200 cursor-not-allowed" @else @click="showConfirmModal = true" class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-xs flex items-center gap-2 cursor-pointer" @endif
                        wire:loading.attr="disabled"
                    >
                        <i wire:loading.remove wire:target="saveSlot4AndClockOut" class="ri-logout-box-r-line text-sm"></i>
                        <i wire:loading wire:target="saveSlot4AndClockOut" class="ri-loader-4-line animate-spin text-sm"></i>
                        <span>Confirm 4th Slot & Clock Out</span>
                    </button>
                </div>
            </div>

        @else
            <!-- Day Completed Banner -->
            <div class="p-5 rounded-md border border-emerald-200 bg-emerald-50/60 text-emerald-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-950">Shift Activity Tracking Completed!</h3>
                        <p class="text-xs text-emerald-800">You have completed Slot 1, 2-Hr Check-In, Lunch Break, 3rd Slot, and 4th Slot Clock-Out for today.</p>
                    </div>
                </div>
                <span class="text-xs font-semibold text-emerald-700 bg-white px-3 py-1.5 rounded-md border border-emerald-200">
                    Shift Ended: {{ $tracking->slot4_checkin_time?->format('g:i A') }}
                </span>
            </div>
        @endif
    </div>

    <!-- Summary Card -->
    @if($tracking)
        <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Today's Shift Summary</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- Slot 2 Details Card -->
                <div class="p-4 rounded-md border space-y-2 {{ $tracking->slot2_is_flagged ? 'bg-red-50/60 border-red-200' : 'bg-slate-50/80 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold uppercase tracking-wide text-xs {{ $tracking->slot2_is_flagged ? 'text-red-800' : 'text-slate-800' }}">Slot 2 Check-In</span>
                        @if($tracking->slot2_is_flagged)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-600 text-white">
                                Exceeded (+{{ $tracking->slot2_deviation_minutes }}m)
                            </span>
                        @elseif($tracking->slot2_checkin_time)
                            <span class="text-emerald-700 font-semibold">✓ {{ $tracking->slot2_checkin_time?->format('g:i A') }} (On Schedule)</span>
                        @else
                            <span class="text-slate-400">Pending</span>
                        @endif
                    </div>
                </div>

                <!-- 3rd Slot Details Card -->
                <div class="p-4 rounded-md border space-y-2 bg-slate-50/80 border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold uppercase tracking-wide text-xs text-slate-800">3rd Slot Start</span>
                        @if($tracking->slot3_start_time)
                            <span class="text-emerald-700 font-semibold">✓ {{ $tracking->slot3_start_time?->format('g:i A') }}</span>
                        @else
                            <span class="text-slate-400">Pending</span>
                        @endif
                    </div>
                </div>

                <!-- 4th Slot Details Card -->
                <div class="p-4 rounded-md border space-y-2 {{ $tracking->slot4_is_flagged ? 'bg-red-50/60 border-red-200' : 'bg-slate-50/80 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold uppercase tracking-wide text-xs {{ $tracking->slot4_is_flagged ? 'text-red-800' : 'text-slate-800' }}">4th Slot & Clock Out</span>
                        @if($tracking->slot4_is_flagged)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-600 text-white">
                                Exceeded (+{{ $tracking->slot4_deviation_minutes }}m)
                            </span>
                        @elseif($tracking->slot4_checkin_time)
                            <span class="text-emerald-700 font-semibold">✓ {{ $tracking->slot4_checkin_time?->format('g:i A') }} (Clocked Out)</span>
                        @else
                            <span class="text-slate-400">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Clock-Out Confirmation Modal -->
    <div 
        x-show="showConfirmModal" 
        x-cloak 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        @keydown.escape.window="showConfirmModal = false"
    >
        <div class="bg-white rounded-md border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4 text-left relative" @click.away="showConfirmModal = false">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-md bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center font-bold text-xl shrink-0">
                    <i class="ri-logout-box-r-line"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Confirm Shift Clock-Out</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Are you sure you want to complete your 4th working slot and clock out for today?
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button 
                    type="button" 
                    @click="showConfirmModal = false" 
                    class="px-4 py-2 text-xs font-medium rounded-md border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                >
                    Cancel
                </button>
                <button 
                    type="button" 
                    wire:click="saveSlot4AndClockOut" 
                    @click="showConfirmModal = false" 
                    class="px-4 py-2 text-xs font-semibold rounded-md text-white bg-slate-900 hover:bg-slate-800 transition-colors cursor-pointer shadow-xs flex items-center gap-1.5"
                >
                    <i class="ri-check-line text-sm"></i>
                    <span>Confirm & Clock Out</span>
                </button>
            </div>
        </div>
    </div>
</div>
