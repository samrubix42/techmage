<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-white border border-slate-200 rounded-md p-6 sm:p-8 relative overflow-hidden shadow-xs">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-primary-light text-primary-dark border border-yellow-200 mb-3">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                Admin System Active
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">System Control Panel</h1>
            <p class="text-slate-600 mt-2 text-sm leading-relaxed">
                Welcome back, <strong class="text-slate-900">{{ auth()->user()->name }}</strong>! Monitor system health, user roles, security policies, and administrative operations from here.
            </p>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1 -->
        <div class="bg-white border border-slate-200 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Total System Users</span>
                <div class="w-8 h-8 rounded-md bg-primary-light text-primary flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">248</div>
            <p class="text-xs text-emerald-600 mt-1 flex items-center gap-1 font-medium">
                <span>↑ 12%</span> <span class="text-slate-400">from last month</span>
            </p>
        </div>

        <!-- Metric 2 -->
        <div class="bg-white border border-slate-200 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Active Employees</span>
                <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">184</div>
            <p class="text-xs text-emerald-600 mt-1 flex items-center gap-1 font-medium">
                <span>● Active Status</span>
            </p>
        </div>

        <!-- Metric 3 -->
        <div class="bg-white border border-slate-200 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">System Admins</span>
                <div class="w-8 h-8 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">6</div>
            <p class="text-xs text-purple-600 mt-1 font-medium">Full Privileges</p>
        </div>

        <!-- Metric 4 -->
        <div class="bg-white border border-slate-200 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Server Uptime</span>
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">99.98%</div>
            <p class="text-xs text-blue-600 mt-1 font-medium">Optimal Performance</p>
        </div>
    </div>

    <!-- Recent Users Table -->
    <div class="bg-white border border-slate-200 rounded-md p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Seeded System Accounts</h3>
                <p class="text-xs text-slate-500">Current authentication system users and active roles</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="text-xs uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">User Name</th>
                        <th class="px-4 py-3">Email Address</th>
                        <th class="px-4 py-3">Assigned Role</th>
                        <th class="px-4 py-3">Account Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3.5 font-semibold text-slate-900 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-primary-light text-primary-dark border border-yellow-200 flex items-center justify-center font-bold text-xs">
                                AU
                            </div>
                            Admin User
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">admin@admin.com</td>
                        <td class="px-4 py-3.5">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-primary-light text-primary-dark border border-yellow-200 uppercase">
                                admin
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3.5 font-semibold text-slate-900 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs">
                                EU
                            </div>
                            Employee User
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">employee@employee.com</td>
                        <td class="px-4 py-3.5">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                employee
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
