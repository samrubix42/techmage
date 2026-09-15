<div 
    x-data="{ 
        isMobile: false,
        checkDevice() {
            // 1. Check Modern UserAgentData (Chrome / Edge / Opera on mobile)
            if (navigator.userAgentData && typeof navigator.userAgentData.mobile === 'boolean') {
                if (navigator.userAgentData.mobile) {
                    this.isMobile = true;
                    return;
                }
            }

            // 2. Check User Agent String for Mobile / Tablet / Phone Keywords
            const ua = (navigator.userAgent || navigator.vendor || window.opera || '').toLowerCase();
            const isMobileUA = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini|mobile|crios|fxios/i.test(ua);

            // 3. Check Hardware Screen Dimensions & Touch Pointers (Detects Phones using Desktop View)
            const isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
            const hardwareWidth = (window.screen && window.screen.width) ? window.screen.width : window.innerWidth;
            const hardwareHeight = (window.screen && window.screen.height) ? window.screen.height : window.innerHeight;
            const isSmallHardwareDevice = Math.min(hardwareWidth, hardwareHeight) < 768;

            // Restrict if it's a mobile User Agent OR a Touch device with phone/small hardware screen dimensions
            this.isMobile = isMobileUA || (isTouchDevice && isSmallHardwareDevice);
        }
    }" 
    x-init="checkDevice(); window.addEventListener('resize', () => checkDevice())"
    x-show="isMobile"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6"
>
    <div class="bg-white border border-slate-200 rounded-md p-6 sm:p-8 max-w-md w-full shadow-2xl text-center space-y-5">
        <!-- Desktop Icon Badge -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-md bg-primary-light text-primary border border-yellow-200 shadow-xs mb-1">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <!-- Warning Heading -->
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700 border border-red-200 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                Mobile Device Access Restricted
            </span>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Desktop Device Required</h2>
            <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                For security and workspace optimization, accessing and logging into <strong>this portal</strong> is strictly prohibited on mobile devices and phone hardware.
            </p>
        </div>

        <!-- Details Box -->
        <div class="p-3.5 rounded-md bg-slate-50 border border-slate-200 text-xs text-slate-600 text-left space-y-1.5">
            <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Device Requirement Notice:
            </div>
            <ul class="list-disc list-inside text-slate-500 space-y-1">
                <li>Please switch to a desktop or laptop computer</li>
                <li>Desktop mode on mobile devices is not permitted</li>
            </ul>
        </div>
    </div>
</div>
