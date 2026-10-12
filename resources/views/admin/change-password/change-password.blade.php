<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Page Header Section -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    <i class="ri-shield-keyhole-line text-amber-600"></i>
                    <span>Account Security</span>
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Change Password</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Ensure your administrator account stays protected by using a strong, unique password.</p>
        </div>

        <div>
            <a 
                href="{{ route('admin.dashboard') }}" 
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-md text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-2xs transition-colors cursor-pointer"
            >
                <i class="ri-arrow-left-line text-sm text-slate-400"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Success Feedback Alert -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md p-4 flex items-center justify-between shadow-xs transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="ri-checkbox-circle-fill text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ session('status') }}</p>
                    <p class="text-xs text-emerald-700 mt-0.5">Your login credentials have been securely updated.</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 cursor-pointer">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Main Form Card (2 cols on large screen) -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-md p-6 sm:p-7 shadow-xs" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h2 class="text-base font-bold text-slate-900">Update Credentials</h2>
                <p class="text-xs text-slate-500 mt-0.5">Enter your existing password and choose a new password with at least 8 characters.</p>
            </div>

            <form wire:submit.prevent="updatePassword" class="space-y-5">
                <!-- Current Password Field -->
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ri-lock-line text-base"></i>
                        </div>
                        <input 
                            :type="showCurrent ? 'text' : 'password'" 
                            id="current_password" 
                            wire:model="current_password"
                            placeholder="Enter your current password"
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border {{ $errors->has('current_password') ? 'border-rose-300 bg-rose-50/20 ring-1 ring-rose-500/20' : 'border-slate-200' }} rounded-md text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-amber-600 focus:ring-2 focus:ring-amber-500/10 transition-colors"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            @click="showCurrent = !showCurrent" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            tabindex="-1"
                        >
                            <i :class="showCurrent ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-base"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- New Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ri-key-2-line text-base"></i>
                        </div>
                        <input 
                            :type="showNew ? 'text' : 'password'" 
                            id="password" 
                            wire:model="password"
                            placeholder="At least 8 characters"
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border {{ $errors->has('password') ? 'border-rose-300 bg-rose-50/20 ring-1 ring-rose-500/20' : 'border-slate-200' }} rounded-md text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-amber-600 focus:ring-2 focus:ring-amber-500/10 transition-colors"
                            required
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            @click="showNew = !showNew" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            tabindex="-1"
                        >
                            <i :class="showNew ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-base"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Confirm New Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ri-check-line text-base"></i>
                        </div>
                        <input 
                            :type="showConfirm ? 'text' : 'password'" 
                            id="password_confirmation" 
                            wire:model="password_confirmation"
                            placeholder="Re-type new password"
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border {{ $errors->has('password_confirmation') ? 'border-rose-300 bg-rose-50/20 ring-1 ring-rose-500/20' : 'border-slate-200' }} rounded-md text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-amber-600 focus:ring-2 focus:ring-amber-500/10 transition-colors"
                            required
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            @click="showConfirm = !showConfirm" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            tabindex="-1"
                        >
                            <i :class="showConfirm ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-base"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Submit / Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a 
                        href="{{ route('admin.dashboard') }}" 
                        class="px-4 py-2.5 rounded-md text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                    >
                        Cancel
                    </a>

                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="updatePassword"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-xs font-semibold rounded-md shadow-xs transition-all disabled:opacity-60 cursor-pointer"
                    >
                        <span wire:loading.remove wire:target="updatePassword" class="flex items-center gap-1.5">
                            <i class="ri-lock-password-line text-sm"></i>
                            <span>Update Password</span>
                        </span>
                        <span wire:loading wire:target="updatePassword" class="flex items-center gap-1.5">
                            <i class="ri-loader-4-line text-sm animate-spin"></i>
                            <span>Updating...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side: Info & Guidelines Cards -->
        <div class="space-y-4">
            <!-- Account Info Card -->
            <div class="bg-white border border-slate-200/80 rounded-md p-5 shadow-xs">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-md bg-amber-100 border border-amber-200 flex items-center justify-center font-bold text-amber-800 text-sm shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'admin@admin.com' }}</p>
                    </div>
                </div>

                <div class="space-y-2 pt-3 border-t border-slate-100 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Role:</span>
                        <span class="font-semibold text-slate-800 bg-amber-50 text-amber-800 px-2 py-0.5 rounded border border-amber-200 text-[11px]">Administrator</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Status:</span>
                        <span class="font-semibold text-emerald-700 flex items-center gap-1 text-[11px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Security Requirements Card -->
            <div class="bg-white border border-slate-200/80 rounded-md p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-3 text-slate-900 font-bold text-xs">
                    <i class="ri-shield-check-line text-emerald-600 text-base"></i>
                    <span>Password Requirements</span>
                </div>
                <ul class="space-y-2 text-xs text-slate-600">
                    <li class="flex items-start gap-2">
                        <i class="ri-check-line text-emerald-600 text-sm shrink-0 mt-0.5"></i>
                        <span>Minimum of 8 characters in length</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ri-check-line text-emerald-600 text-sm shrink-0 mt-0.5"></i>
                        <span>Must differ from your current password</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ri-check-line text-emerald-600 text-sm shrink-0 mt-0.5"></i>
                        <span>Confirmed accurately in both fields</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
