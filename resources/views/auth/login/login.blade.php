<div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-200/40 transition-all">
    <!-- Brand Logo & Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-600 text-white font-bold text-xl shadow-sm mb-3">
            TM
        </div>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Sign In to TechMage</h2>
        <p class="text-xs text-slate-500 mt-1">Enter your email and password to access your account</p>
    </div>

    <!-- Session / General Errors -->
    @if (session('error'))
        <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-medium flex items-center gap-2">
            <i class="ri-error-warning-line text-base text-red-600 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Login Form -->
    <form wire:submit.prevent="login" class="space-y-4">
        <!-- Email Input -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Address</label>
            <div class="relative">
                <input 
                    type="email" 
                    id="email" 
                    wire:model="email"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-amber-600 focus:ring-2 focus:ring-amber-500/10 transition-colors"
                    placeholder="name@company.com"
                    required
                    autofocus
                >
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="ri-mail-line text-base"></i>
                </div>
            </div>
            @error('email')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Input -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
            <div class="relative">
                <input 
                    type="password" 
                    id="password" 
                    wire:model="password"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-amber-600 focus:ring-2 focus:ring-amber-500/10 transition-colors"
                    placeholder="••••••••"
                    required
                >
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="ri-lock-2-line text-base"></i>
                </div>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input 
                    type="checkbox" 
                    wire:model="remember"
                    class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer"
                >
                <span class="text-xs text-slate-600 font-medium">Remember me</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            wire:target="login"
            class="w-full h-11 py-2.5 px-4 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed cursor-pointer"
        >
            <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                <span>Sign In</span>
                <i class="ri-arrow-right-line text-base"></i>
            </span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <i class="ri-loader-4-line text-lg animate-spin"></i>
                <span>Signing in...</span>
            </span>
        </button>
    </form>

    <!-- Demo Credentials Section -->
    <div class="mt-8 pt-6 border-t border-slate-100">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center mb-3">Quick Demo Accounts</p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button 
                type="button"
                @click="$wire.set('email', 'admin@admin.com'); $wire.set('password', 'password');"
                class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-colors cursor-pointer group"
            >
                <div class="font-semibold text-slate-800 flex items-center justify-between">
                    <span>Admin</span>
                    <i class="ri-shield-user-line text-amber-600"></i>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate">admin@admin.com</div>
            </button>

            <button 
                type="button"
                @click="$wire.set('email', 'employee@employee.com'); $wire.set('password', 'password');"
                class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-colors cursor-pointer group"
            >
                <div class="font-semibold text-slate-800 flex items-center justify-between">
                    <span>Employee</span>
                    <i class="ri-user-line text-slate-500"></i>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate">employee@...</div>
            </button>
        </div>
    </div>
</div>