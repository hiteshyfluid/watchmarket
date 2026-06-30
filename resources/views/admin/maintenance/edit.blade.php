@extends('layouts.admin')
@section('title', 'Maintenance Mode')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Maintenance Mode</h2>
        <p class="text-sm text-gray-500 mt-1">When enabled, a maintenance screen overlays the public site. Visitors can bypass it by entering the site access password.</p>
    </div>

    {{-- Status card --}}
    <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 rounded-full {{ $enabled ? 'bg-red-500' : 'bg-green-500' }}"></div>
            <span class="text-sm font-medium text-gray-700">
                Maintenance mode is currently <strong>{{ $enabled ? 'ENABLED' : 'DISABLED' }}</strong>
            </span>
        </div>

        @if($enabled)
            <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 rounded">
                The public site is currently showing a maintenance screen. Visitors must enter the access password to view content.
            </div>
        @else
            <div class="bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded">
                The public site is live and fully accessible to all visitors.
            </div>
        @endif

        <p class="text-xs text-gray-500">The admin panel is not affected by maintenance mode.</p>
    </div>

    {{-- Toggle form --}}
    <form method="POST" action="{{ route('admin.maintenance.update') }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="enabled" value="{{ $enabled ? '0' : '1' }}">
        <button type="submit"
            class="{{ $enabled ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white px-5 py-2 rounded text-sm font-medium">
            {{ $enabled ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode' }}
        </button>
    </form>

    {{-- Password section --}}
    <div class="border-t border-gray-200 pt-6">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Access Password</h3>
        <p class="text-sm text-gray-500 mb-4">This is the password visitors must enter to bypass the maintenance screen.</p>

        <form method="POST" action="{{ route('admin.maintenance.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <section class="bg-white rounded-lg shadow-sm p-6 space-y-3">
                <label class="block text-sm font-medium text-gray-700">Current password</label>
                <div x-data="{ show: false }" class="relative">
                    <input
                        :type="show ? 'text' : 'password'"
                        name="password"
                        value="{{ old('password', $password) }}"
                        required
                        minlength="6"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-1 focus:ring-gray-400"
                    />
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 w-10 flex items-center justify-center text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                        <svg x-cloak x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.477 10.485A3 3 0 0013.5 12a3 3 0 01-3.023-1.515zM6.228 6.228A9.77 9.77 0 002.25 12s3.75 7.5 9.75 7.5a9.77 9.77 0 005.521-1.729M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0A3 3 0 0110.5 12a3 3 0 011.515-2.977m3.757 3.757A9.77 9.77 0 0121.75 12s-3.75-7.5-9.75-7.5a9.77 9.77 0 00-2.522.338"/></svg>
                    </button>
                </div>
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-500">Minimum 6 characters. Changing this will invalidate any active visitor sessions that already bypassed the screen.</p>
            </section>

            <div>
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
