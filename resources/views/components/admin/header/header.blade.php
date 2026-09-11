<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-10 sticky top-0">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 hover:text-slate-800 p-2 rounded-md hover:bg-slate-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Page Header / Title -->
        <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Control Center</h1>
            <p class="text-xs text-slate-500 hidden sm:block">Overview & System Administration</p>
        </div>
    </div>

    <!-- Right Header Actions -->
    <div class="flex items-center gap-4">
        <!-- Role Badge with Primary Dark Yellow -->
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-primary-light text-primary-dark border border-yellow-200">
            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
            Admin Portal
        </span>

        <!-- User Dropdown (Alpine) -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-slate-900 focus:outline-none cursor-pointer">
                <div class="w-8 h-8 rounded-md bg-primary text-white flex items-center justify-center font-bold shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="hidden sm:inline-block font-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
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
                class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-md shadow-lg py-1 z-50"
            >
                <div class="px-4 py-2 border-b border-slate-100">
                    <p class="text-xs text-slate-400">Signed in as</p>
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>

                <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-50 flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </div>
        </div>
    </div>
</header>