<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($adminPageTitle = trim($__env->yieldContent('title')) !== '' ? trim($__env->yieldContent('title')) : 'Admin')
    <title>{{ $seoMeta['title'] ?? ($adminPageTitle . ' - ' . config('app.name', 'WatchMarket')) }}</title>
    <meta name="description" content="{{ $seoMeta['description'] ?? config('app.name', 'WatchMarket Admin') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: true }">

<div class="flex h-screen overflow-hidden">

    {{-- ============================================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================================ --}}
    <aside
        class="flex flex-col bg-gray-900 text-gray-300 transition-all duration-300 overflow-y-auto"
        :class="sidebarOpen ? 'w-64' : 'w-16'"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-700 min-h-[64px]">
            <div class="w-8 h-8 bg-yellow-500 rounded flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
            </div>
            <span class="font-bold text-white text-sm uppercase tracking-widest whitespace-nowrap" x-show="sidebarOpen">WM Admin</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2 py-4 space-y-1 text-sm">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </a>

            {{-- Users --}}
            <a href="{{ route('admin.users') }}"
               class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.users') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Users</span>
            </a>

            {{-- Adverts --}}
            <a href="{{ route('admin.adverts.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.adverts.*') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Adverts</span>
            </a>

            {{-- SEO Meta --}}
            <a href="{{ route('admin.seo.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.seo.*') ? 'bg-gray-700 text-white' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h8m-8 4h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">SEO Meta</span>
            </a>

            {{-- Memberships --}}
            <div x-data="{ open: {{ request()->routeIs('admin.membership-levels.*') || request()->routeIs('admin.membership-members.*') || request()->routeIs('admin.membership-orders.*') || request()->routeIs('admin.membership-settings.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 text-left"
                    x-show="sidebarOpen">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6m10 0H7"/></svg>
                    <span class="flex-1 whitespace-nowrap font-medium">Memberships</span>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <a href="{{ route('admin.membership-levels.index') }}" x-show="!sidebarOpen"
                   class="flex items-center justify-center px-3 py-2 rounded hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6m10 0H7"/></svg>
                </a>

                <div x-show="open && sidebarOpen" class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('admin.membership-levels.index') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.membership-levels.*') ? 'text-white' : 'text-gray-400' }}">
                        Levels
                    </a>
                    <a href="{{ route('admin.membership-members.index') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.membership-members.*') ? 'text-white' : 'text-gray-400' }}">
                        Members
                    </a>
                    <a href="{{ route('admin.membership-orders.index') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.membership-orders.*') ? 'text-white' : 'text-gray-400' }}">
                        Orders
                    </a>
                    <a href="{{ route('admin.membership-settings.edit') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.membership-settings.*') ? 'text-white' : 'text-gray-400' }}">
                        Billing Settings
                    </a>
                </div>
            </div>

            {{-- PRODUCTS section --}}
            <div x-data="{ open: {{ request()->routeIs('admin.brands.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.tags.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 text-left"
                    x-show="sidebarOpen">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="flex-1 whitespace-nowrap font-medium">Products</span>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                {{-- Products icon-only for collapsed sidebar --}}
                <a href="{{ route('admin.brands.index') }}" x-show="!sidebarOpen"
                   class="flex items-center justify-center px-3 py-2 rounded hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </a>

                <div x-show="open && sidebarOpen" class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('admin.brands.index') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.brands.*') ? 'text-white' : 'text-gray-400' }}">
                        Brands &amp; Models
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-gray-400' }}">
                        Categories
                    </a>
                    <a href="{{ route('admin.tags.index') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.tags.*') ? 'text-white' : 'text-gray-400' }}">
                        Tags
                    </a>
                </div>
            </div>

            {{-- PRODUCT ADDITIONAL FIELDS section --}}
            <div x-data="{ open: {{ request()->routeIs('admin.attributes.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 text-left"
                    x-show="sidebarOpen">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span class="flex-1 whitespace-nowrap font-medium text-xs leading-tight">Product Additional Fields</span>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <a href="{{ route('admin.attributes.index', 'condition') }}" x-show="!sidebarOpen"
                   class="flex items-center justify-center px-3 py-2 rounded hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </a>

                <div x-show="open && sidebarOpen" class="ml-8 mt-1 space-y-1">
                    @foreach(\App\Models\AttributeOption::TYPES as $key => $label)
                    <a href="{{ route('admin.attributes.index', $key) }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-xs hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.attributes.*') && request()->route('type') === $key ? 'text-white' : 'text-gray-400' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>
        </nav>

        {{-- Toggle button --}}
        <div class="px-2 py-4 border-t border-gray-700">
            <button @click="sidebarOpen = !sidebarOpen"
                class="flex items-center justify-center w-full py-2 rounded hover:bg-gray-700 text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </aside>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT AREA --}}
    {{-- ============================================================ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 flex-shrink-0">
            <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-800">Logout</button>
                </form>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>

