@php
    $maintenanceModeOn = \App\Models\SiteSetting::getValue('maintenance_mode', '0') === '1' && !request()->routeIs('superadmin.login');
    $maintenancePassword = \App\Models\SiteSetting::getValue('maintenance_password', 'Watch2026');
@endphp
@if($maintenanceModeOn)
<div
    x-data="{
        show: !sessionStorage.getItem('wm_maintenance_bypass'),
        pwd: '',
        error: false,
        check() {
            if (this.pwd === {{ Js::from($maintenancePassword) }}) {
                sessionStorage.setItem('wm_maintenance_bypass', '1');
                this.show = false;
                this.error = false;
            } else {
                this.error = true;
                this.pwd = '';
            }
        }
    }"
    x-show="show"
    class="fixed inset-0 z-[9999] bg-gray-950 flex flex-col items-center justify-center p-6"
>
    <div class="max-w-sm w-full text-center space-y-8">

        {{-- Logo / icon --}}
        <div class="flex justify-center">
            <div class="w-20 h-20 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-10 h-10 text-black" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        {{-- Text --}}
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-white tracking-tight">Under Maintenance</h1>
            <p class="text-gray-400 text-sm leading-relaxed">
                We're currently making some improvements to the site.<br>
                We'll be back shortly.
            </p>
        </div>

        {{-- Password bypass --}}
        <div class="space-y-3">
            <input
                x-model="pwd"
                @keydown.enter="check()"
                type="password"
                placeholder="Enter access password"
                class="w-full px-4 py-3 rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
            />
            <p x-cloak x-show="error" class="text-red-400 text-xs">Incorrect password. Please try again.</p>
            <button
                @click="check()"
                type="button"
                class="w-full bg-yellow-500 text-black font-semibold py-3 rounded-lg text-sm hover:bg-yellow-400 transition-colors"
            >
                Continue to Site
            </button>
        </div>

    </div>
</div>
@endif
