<div class="bg-white border border-slate-200 rounded-md p-6 sm:p-8 shadow-sm">
    <!-- Brand Logo & Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-md bg-primary text-white font-bold text-xl shadow-sm mb-3">
            TM
        </div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Sign In to TechMage</h2>
        <p class="text-sm text-slate-500 mt-1">Enter your details to access your portal</p>
    </div>

    <!-- Session / General Errors -->
    @if (session('error'))
        <div class="mb-5 p-3.5 rounded-md bg-red-50 border border-red-200 text-xs text-red-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Login Form -->
    <form wire:submit.prevent="login" class="space-y-5">
        <!-- Email Input -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
            <input 
                type="email" 
                id="email" 
                wire:model="email"
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-md text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-yellow-600 focus:ring-1 focus:ring-yellow-600 transition-colors"
                placeholder="admin@admin.com or employee@employee.com"
                required
                autofocus
            >
            @error('email')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Input -->
        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
            <input 
                type="password" 
                id="password" 
                wire:model="password"
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-md text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-yellow-600 focus:ring-1 focus:ring-yellow-600 transition-colors"
                placeholder="••••••••"
                required
            >
            @error('password')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    wire:model="remember"
                    class="w-4 h-4 rounded-md border-slate-300 text-yellow-600 focus:ring-yellow-500"
                >
                <span class="text-xs text-slate-600">Remember me</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full py-2.5 px-4 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-md shadow-sm transition-colors flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Sign In</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Authenticating...
            </span>
        </button>
    </form>

    <!-- Demo Credentials Hint Box -->
    <div class="mt-8 pt-6 border-t border-slate-100">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider text-center mb-3">Quick Demo Credentials</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
            <button 
                type="button"
                @click="$wire.set('email', 'admin@admin.com'); $wire.set('password', 'password');"
                class="p-2.5 rounded-md bg-primary-light border border-yellow-200 text-left hover:bg-yellow-100 transition-colors group cursor-pointer"
            >
                <div class="font-semibold text-primary-dark">Admin Account</div>
                <div class="text-slate-600 text-[11px]">admin@admin.com</div>
                <div class="text-slate-500 text-[10px]">Pass: password</div>
            </button>

            <button 
                type="button"
                @click="$wire.set('email', 'employee@employee.com'); $wire.set('password', 'password');"
                class="p-2.5 rounded-md bg-slate-100 border border-slate-200 text-left hover:bg-slate-200/70 transition-colors group cursor-pointer"
            >
                <div class="font-semibold text-slate-800">Employee Account</div>
                <div class="text-slate-600 text-[11px]">employee@employee.com</div>
                <div class="text-slate-500 text-[10px]">Pass: password</div>
            </button>
        </div>
    </div>
</div>