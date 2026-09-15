<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-10 sticky top-0 shadow-2xs">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 hover:text-slate-900 p-2 rounded-md hover:bg-slate-100 transition-colors">
            <i class="ri-menu-line text-xl"></i>
        </button>

        <!-- Page Header / Greeting -->
        <div>
            <h1 class="text-base font-bold text-slate-900 leading-tight flex items-center gap-2">
                <span>Welcome back, {{ auth()->user()->name ?? 'Employee' }}</span>
                <span class="inline-block animate-bounce">👋</span>
            </h1>
            <p class="text-[11px] font-medium text-slate-500 hidden sm:block">Employee Portal</p>
        </div>
    </div>

    <!-- Right Header Actions -->
    <div class="flex items-center gap-3">
        <!-- Role Badge -->
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100/80 text-slate-700 border border-slate-200">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
            Employee
        </span>

        <!-- User Dropdown (Alpine) -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 text-xs font-semibold text-slate-700 hover:text-slate-900 focus:outline-none cursor-pointer p-1 rounded-md hover:bg-slate-100 transition-colors">
                <div class="w-8 h-8 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)) }}
                </div>
                <span class="hidden sm:inline-block font-medium text-slate-900">{{ auth()->user()->name ?? 'Employee' }}</span>
                <i class="ri-arrow-down-s-line text-slate-400 text-base"></i>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-52 bg-white border border-slate-200 rounded-md shadow-lg py-1 z-50 overflow-hidden"
            >
                <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50/50">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Signed in as</p>
                    <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>

                <a href="{{ route('employee.settings') }}" class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-2 transition-colors">
                    <i class="ri-settings-3-line text-slate-400 text-sm"></i>
                    <span>Schedule Settings</span>
                </a>

                <button wire:click="logout" class="w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors cursor-pointer">
                    <i class="ri-logout-box-r-line text-red-500 text-sm"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </div>
</header>