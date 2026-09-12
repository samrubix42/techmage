<aside 
    class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 md:translate-x-0 fixed md:static inset-y-0 left-0 z-40"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <!-- Top Header / Branding -->
    <div>
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold shadow-xs">
                    TM
                </div>
                <div>
                    <span class="font-bold text-slate-900 tracking-wide text-base block leading-tight">TechMage</span>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee Portal</span>
                </div>
            </div>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('employee.dashboard') ? 'bg-slate-100 text-slate-900 font-semibold border border-slate-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                My Dashboard
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                My Tasks & Projects
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                My Profile
            </a>
            <a href="{{ route('employee.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('employee.settings') ? 'bg-slate-100 text-slate-900 font-semibold border border-slate-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('employee.settings') ? 'text-slate-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Schedule Settings
            </a>
        </nav>
    </div>

    <!-- User Profile & Logout Footer -->
    <div class="p-4 border-t border-slate-200 space-y-3">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 rounded-md bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-700">
                {{ strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'Employee' }}</p>
                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? 'employee@employee.com' }}</p>
            </div>
        </div>

        <button 
            wire:click="logout" 
            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Sign Out
        </button>
    </div>
</aside>