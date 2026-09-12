<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                    Employee Portal
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Work Schedule & Timing Settings</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Configure your standard daily login shift time and lunch break schedule.</p>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md p-4 flex items-center justify-between shadow-xs transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ session('message') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Settings Form Card -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">Shift Timing Configuration</h2>
            <p class="text-xs text-slate-500 mt-0.5">These timings will be referenced for work logs and attendance helpers.</p>
        </div>

        <form wire:submit.prevent="saveSettings" class="p-6 space-y-6">
            <!-- Login Time Section -->
            <div class="space-y-2">
                <label for="loginTime" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    Expected Daily Login Time <span class="text-rose-500">*</span>
                </label>
                <div class="relative max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <input 
                        id="loginTime"
                        type="time" 
                        wire:model="loginTime"
                        class="w-full pl-9 pr-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white {{ $errors->has('loginTime') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                    />
                </div>
                @error('loginTime') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400">Set the default hour when your shift starts each workday.</p>
            </div>

            <hr class="border-slate-100" />

            <!-- Lunch Time Section -->
            <div class="space-y-4">
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    Lunch Break Schedule <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                    <!-- Lunch Start -->
                    <div>
                        <label for="lunchStartTime" class="block text-xs text-slate-500 mb-1">Lunch Start Time</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input 
                                id="lunchStartTime"
                                type="time" 
                                wire:model="lunchStartTime"
                                class="w-full pl-9 pr-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white {{ $errors->has('lunchStartTime') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                        </div>
                        @error('lunchStartTime') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Lunch End -->
                    <div>
                        <label for="lunchEndTime" class="block text-xs text-slate-500 mb-1">Lunch End Time</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input 
                                id="lunchEndTime"
                                type="time" 
                                wire:model="lunchEndTime"
                                class="w-full pl-9 pr-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white {{ $errors->has('lunchEndTime') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                        </div>
                        @error('lunchEndTime') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p class="text-xs text-slate-400">Define your standard start and end time for mid-day lunch breaks.</p>
            </div>

            <!-- Schedule Live Summary Box -->
            <div class="p-4 rounded-md bg-slate-50 border border-slate-200 text-xs space-y-2">
                <div class="font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Schedule Overview (Live Preview):
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-600">
                    <div>
                        <strong>Login Time:</strong> {{ $loginTime ? $loginTime : 'Not configured' }}
                    </div>
                    <div>
                        <strong>Lunch Window:</strong> {{ $lunchStartTime ? $lunchStartTime : '--:--' }} to {{ $lunchEndTime ? $lunchEndTime : '--:--' }}
                    </div>
                </div>
            </div>

            <!-- Submit Button Footer -->
            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button 
                    type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 active:scale-95 rounded-md transition-all cursor-pointer shadow-sm"
                >
                    <span wire:loading.remove wire:target="saveSettings">Save Schedule Settings</span>
                    <span wire:loading wire:target="saveSettings" class="flex items-center gap-1.5">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>