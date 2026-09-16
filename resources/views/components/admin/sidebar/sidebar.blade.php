<aside 
    class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 md:translate-x-0 fixed md:static inset-y-0 left-0 z-40 shadow-xs"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <!-- Brand Header -->
    <div>
        <div class="py-4 flex items-center justify-between px-5 border-b border-slate-200">
            <div class="flex flex-col items-start gap-1">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="h-9 w-auto rounded-md object-contain shrink-0">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Admin Portal</span>
            </div>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 p-1">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="px-3 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-dashboard-3-line text-base {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.employees') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('admin.employee*') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-team-line text-base {{ request()->routeIs('admin.employee*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Employee Management</span>
            </a>

            <a href="{{ route('admin.departments') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('admin.departments') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-building-4-line text-base {{ request()->routeIs('admin.departments') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Departments</span>
            </a>

            <a href="{{ route('admin.daily-task-reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('admin.daily-task-reports') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-file-paper-2-line text-base {{ request()->routeIs('admin.daily-task-reports') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Daily Task Reports</span>
            </a>

            <a href="{{ route('admin.attendance-logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('admin.attendance-logs') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-time-line text-base {{ request()->routeIs('admin.attendance-logs') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Employee Working Hours & Slots</span>
            </a>

            <a href="{{ route('admin.leave-requests') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('admin.leave-requests') ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i class="ri-calendar-todo-line text-base {{ request()->routeIs('admin.leave-requests') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Leave Requests</span>
            </a>
        </nav>
    </div>

    <!-- User Profile & Logout Footer -->
    <div class="p-3.5 border-t border-slate-200 space-y-3 bg-slate-50/50">
        <div class="flex items-center gap-3 px-1">
            <div class="w-8 h-8 rounded-md bg-amber-100 border border-amber-200 flex items-center justify-center font-bold text-amber-800 text-xs shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden min-w-0">
                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
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