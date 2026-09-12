<aside 
    class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 md:translate-x-0 fixed md:static inset-y-0 left-0 z-40"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div>
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold shadow-xs">
                    TM
                </div>
                <div>
                    <span class="font-bold text-slate-900 tracking-tight text-sm block leading-tight">TechMage</span>
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Employee Portal</span>
                </div>
            </div>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 p-1">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        <!-- Clean Minimal Attendance / Clock In Card -->
        <div class="p-3.5">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                <!-- Date & Status Indicator Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                        <i class="ri-calendar-line text-slate-500"></i>
                        <span>{{ $todayDateFormatted }}</span>
                    </div>

                    @if($activeLog)
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Working
                        </span>
                    @elseif($todayAttendance && $todayLogs->isNotEmpty())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            On Break
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 border border-slate-300">
                            Not Clocked In
                        </span>
                    @endif
                </div>

                <!-- Clock In Details or Timing Info -->
                @if($activeLog && $activeLog->clock_in_time)
                    <div class="bg-white rounded-lg p-2.5 border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Clock In Time:</span>
                        <span class="font-bold text-slate-900 flex items-center gap-1">
                            <i class="ri-time-line text-blue-600"></i>
                            {{ $activeLog->clock_in_time?->format('g:i A') }}
                        </span>
                    </div>
                @endif

                <!-- Flash Message Notification -->
                @if(session()->has('attendance_status'))
                    <p class="text-[11px] font-medium text-emerald-800 bg-emerald-50 p-2 rounded-lg border border-emerald-200 text-center leading-tight">
                        {{ session('attendance_status') }}
                    </p>
                @endif

                <!-- Dynamic Clock Action Button -->
                <div>
                    @if(!$activeLog)
                        <button 
                            wire:click="clockIn" 
                            wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-xs cursor-pointer disabled:opacity-50"
                        >
                            <i wire:loading.remove wire:target="clockIn" class="ri-login-box-line text-sm"></i>
                            <i wire:loading wire:target="clockIn" class="ri-loader-4-line animate-spin text-sm"></i>
                            <span>Clock In Now</span>
                        </button>
                    @else
                        <button 
                            wire:click="clockOut" 
                            wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 transition-colors shadow-xs cursor-pointer disabled:opacity-50"
                        >
                            <i wire:loading.remove wire:target="clockOut" class="ri-logout-box-r-line text-sm"></i>
                            <i wire:loading wire:target="clockOut" class="ri-loader-4-line animate-spin text-sm"></i>
                            <span>Clock Out Now</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="px-3 py-2 space-y-1">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('employee.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="ri-dashboard-3-line text-base {{ request()->routeIs('employee.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <i class="ri-checkbox-multiple-line text-base text-slate-400"></i>
                <span>My Tasks & Projects</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <i class="ri-user-line text-base text-slate-400"></i>
                <span>My Profile</span>
            </a>

            <a href="{{ route('employee.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('employee.settings') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="ri-settings-4-line text-base {{ request()->routeIs('employee.settings') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Schedule Settings</span>
            </a>
        </nav>
    </div>

    <!-- User Profile & Logout Footer -->
    <div class="p-3.5 border-t border-slate-200 space-y-3">
        <div class="flex items-center gap-3 px-1">
            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)) }}
            </div>
            <div class="overflow-hidden min-w-0">
                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Employee' }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'employee@example.com' }}</p>
            </div>
        </div>

        <button 
            wire:click="logout" 
            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors cursor-pointer"
        >
            <i class="ri-logout-box-r-line"></i>
            <span>Sign Out</span>
        </button>
    </div>
</aside>