<aside 
    class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 md:translate-x-0 fixed md:static inset-y-0 left-0 z-40"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <!-- Brand Header -->
    <div>
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold shadow-xs">
                    TM
                </div>
                <div>
                    <span class="font-bold text-slate-900 tracking-tight text-sm block leading-tight">TechMage</span>
                    <span class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider">Admin Portal</span>
                </div>
            </div>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 p-1">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="px-3 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="ri-dashboard-3-line text-base {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.employees') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('admin.employees') ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="ri-team-line text-base {{ request()->routeIs('admin.employees') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Employee Management</span>
            </a>

            <a href="{{ route('admin.departments') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('admin.departments') ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="ri-building-4-line text-base {{ request()->routeIs('admin.departments') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Departments</span>
            </a>

            <a href="{{ route('admin.daily-task-reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('admin.daily-task-reports') ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="ri-file-paper-2-line text-base {{ request()->routeIs('admin.daily-task-reports') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Daily Task Reports</span>
            </a>

            <a href="{{ route('admin.attendance-logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('admin.attendance-logs') ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="ri-time-line text-base {{ request()->routeIs('admin.attendance-logs') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Employee Working Hours & Slots</span>
            </a>

        </nav>
    </div>

    <!-- User Profile & Logout Footer -->
    <div class="p-3.5 border-t border-slate-200 space-y-3">
        <div class="flex items-center gap-3 px-1">
            <div class="w-8 h-8 rounded-lg bg-amber-100 border border-amber-200 flex items-center justify-center font-bold text-amber-800 text-xs shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden min-w-0">
                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
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