<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-white border border-slate-200 rounded-md p-6 sm:p-8 relative overflow-hidden shadow-xs">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 mb-3">
                <span class="w-2 h-2 rounded-full bg-slate-500 animate-pulse"></span>
                Employee Workspace
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Hello, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-600 mt-2 text-sm leading-relaxed">
                Welcome to your employee portal. Here you can track assigned tasks, monitor your project milestones, and access company resources.
            </p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Stat 1 -->
        <div class="bg-white border border-slate-200 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Assigned Tasks</span>
                <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">8 Active</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">3 Pending Review</p>
        </div>

        <!-- Stat 2 -->
        <div class="bg-white border border-slate-200 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Completed Tasks</span>
                <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">34</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">This Month</p>
        </div>

        <!-- Stat 3 -->
        <div class="bg-white border border-slate-200 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Account Role</span>
                <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div class="text-xl font-bold text-slate-900 capitalize">{{ auth()->user()->role }}</div>
            <p class="text-xs text-emerald-600 mt-1 font-semibold">Account Active</p>
        </div>
    </div>

    <!-- Employee Information Card -->
    <div class="bg-white border border-slate-200 rounded-md p-6 shadow-xs">
        <h3 class="text-base font-bold text-slate-900 mb-4">My Account Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="p-4 rounded-md bg-slate-50 border border-slate-200">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Full Name</span>
                <span class="text-slate-900 font-semibold">{{ auth()->user()->name }}</span>
            </div>

            <div class="p-4 rounded-md bg-slate-50 border border-slate-200">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Email Address</span>
                <span class="text-slate-900 font-semibold">{{ auth()->user()->email }}</span>
            </div>

            <div class="p-4 rounded-md bg-slate-50 border border-slate-200">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Assigned Role</span>
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-200 text-slate-800 uppercase">
                    {{ auth()->user()->role }}
                </span>
            </div>

            <div class="p-4 rounded-md bg-slate-50 border border-slate-200">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Account Status</span>
                <span class="text-emerald-600 font-semibold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active Employee
                </span>
            </div>
        </div>
    </div>
</div>
