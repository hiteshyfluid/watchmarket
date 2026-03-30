@php
    $popularMenuBrands = \App\Models\Brand::parents()
        ->active()
        ->where('is_popular', true)
        ->orderBy('name')
        ->get(['id', 'name']);
    $brandColumnSplit = (int) ceil($popularMenuBrands->count() / 2);
    $popularBrandsLeft = $popularMenuBrands->take($brandColumnSplit);
    $popularBrandsRight = $popularMenuBrands->slice($brandColumnSplit);
@endphp

<header
    class="bg-white border-b border-[#e8e8e8]"
    x-data="{ buyOpen: false, mobileMenuOpen: false, mobileBrandsOpen: false }"
    @keydown.escape.window="buyOpen = false; mobileMenuOpen = false; mobileBrandsOpen = false"
>
    <div class="site-container px-4 lg:px-8">
        <div class="flex h-16 sm:h-20 items-center justify-between gap-3 sm:gap-6">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="lg:hidden inline-flex w-10 h-10 rounded-xl border border-[#e5e5e5] items-center justify-center text-[#222] hover:bg-[#f6f6f6]"
                    @click="mobileMenuOpen = true"
                    aria-label="Open menu"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <a href="/" class="shrink-0">
                    <img
                        src="{{ asset('images/logo.webp') }}"
                        alt="Watch Market logo"
                        class="h-8 sm:h-10 w-auto max-w-[190px] sm:max-w-[250px]"
                    >
                </a>
            </div>

            <nav class="hidden lg:flex items-center gap-0 text-[14px] font-medium text-[#1f1f1f]">
                <a href="{{ route('home') }}" class="text-[14px] py-2 px-4 inline-flex items-center text-[#1f1f1f] no-underline hover:bg-[#f5f5f5]">Home</a>

                <div class="relative" @mouseenter="buyOpen = true" @mouseleave="buyOpen = false">
                    <button type="button"
                        @click="buyOpen = !buyOpen"
                        class="text-[14px] py-2 px-4 inline-flex items-center gap-2 text-[#1f1f1f] hover:bg-[#f5f5f5]">
                        <span>Buy a Watch</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-cloak x-show="buyOpen" x-transition
                        class="absolute left-0 top-full z-50 w-[640px] max-w-[calc(100vw-4rem)] bg-white border border-[#d8d8d8] shadow-[0_12px_28px_rgba(0,0,0,0.12)] p-5">
                        <div class="text-[16px] leading-none font-semibold text-[#4b4b4b] mb-4">POPULAR BRANDS</div>
                        <div class="grid grid-cols-2 gap-10">
                            <div class="space-y-2">
                                @foreach($popularBrandsLeft as $brand)
                                    <a href="{{ route('market.index', ['brands' => [$brand->id]]) }}" class="block text-[14px] text-[#343434] no-underline hover:text-black">
                                        {{ $brand->name }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="space-y-2">
                                @foreach($popularBrandsRight as $brand)
                                    <a href="{{ route('market.index', ['brands' => [$brand->id]]) }}" class="block text-[14px] text-[#343434] no-underline hover:text-black">
                                        {{ $brand->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('market.index') }}" class="text-[14px] py-2 px-4 inline-flex items-center text-[#1f1f1f] no-underline hover:bg-[#f5f5f5]">Search Watches</a>
                <a href="{{ route('sell-watch') }}" class="text-[14px] py-2 px-4 inline-flex items-center text-[#1f1f1f] no-underline hover:bg-[#f5f5f5]">Sell A Watch</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('market.index') }}" class="hidden lg:flex w-10 h-10 rounded-full items-center justify-center text-[#222] hover:bg-[#f5f5f5]" aria-label="Search">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </a>
                <a href="{{ auth()->check() ? route('messages.index') : route('login') }}" class="hidden lg:flex w-10 h-10 rounded-full items-center justify-center text-[#222] hover:bg-[#f5f5f5]" aria-label="Messages">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8m-8 4h5m-9 5l3.6-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h1v3z"/></svg>
                </a>

                @auth
                    <div x-data="{ open: false }" class="relative hidden lg:block">
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-10 h-10 rounded-full flex items-center justify-center bg-[#f5f5f5] text-[#222] hover:bg-[#ececec]"
                            aria-label="Account menu"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </button>

                        <div
                            x-cloak
                            x-show="open"
                            @click.away="open = false"
                            @keydown.escape.window="open = false"
                            x-transition
                            class="absolute right-0 mt-3 w-72 bg-[#f7f7f7] border border-[#d8d8d8] rounded-xl shadow-[0_18px_35px_rgba(0,0,0,0.14)] overflow-hidden z-50"
                        >
                            <div class="px-5 py-4 border-b border-[#e3e3e3]">
                                <div class="text-[18px] font-semibold text-[#222]">{{ auth()->user()->name }}</div>
                                <div class="text-[14px] text-[#6a6a6a]">{{ auth()->user()->email }}</div>
                            </div>

                            <div class="py-2">
                                <a href="{{ route('my-account') }}" class="px-5 py-2.5 flex items-center gap-3 no-underline text-[#222] hover:bg-[#efefef]">
                                    <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4m-18 5l9 4 9-4"/></svg>
                                    <span class="text-[16px]">Dashboard</span>
                                </a>
                                <a href="{{ route('my-account', ['tab' => 'profile']) }}" class="px-5 py-2.5 flex items-center gap-3 no-underline text-[#222] hover:bg-[#efefef]">
                                    <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="text-[16px]">My Profile</span>
                                </a>
                                <a href="{{ route('messages.index') }}" class="px-5 py-2.5 flex items-center gap-3 no-underline text-[#222] hover:bg-[#efefef]">
                                    <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8m-8 4h5m-9 5l3.6-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h1v3z"/></svg>
                                    <span class="text-[16px]">Messages</span>
                                </a>
                                <a href="{{ route('my-account', ['tab' => 'settings']) }}" class="px-5 py-2.5 flex items-center gap-3 no-underline text-[#222] hover:bg-[#efefef]">
                                    <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317a1 1 0 011.35-.936l.4.17a1 1 0 00.79 0l.4-.17a1 1 0 011.35.936l.035.433a1 1 0 00.497.786l.374.216a1 1 0 01.364 1.364l-.216.374a1 1 0 000 .79l.216.374a1 1 0 01-.364 1.364l-.374.216a1 1 0 00-.497.786l-.035.433a1 1 0 01-1.35.936l-.4-.17a1 1 0 00-.79 0l-.4.17a1 1 0 01-1.35-.936l-.035-.433a1 1 0 00-.497-.786l-.374-.216a1 1 0 01-.364-1.364l.216-.374a1 1 0 000-.79l-.216-.374a1 1 0 01.364-1.364l.374-.216a1 1 0 00.497-.786l.035-.433z"/><circle cx="12" cy="8.5" r="2.2" stroke-width="1.8"/></svg>
                                    <span class="text-[16px]">Settings</span>
                                </a>
                            </div>

                            <div class="border-t border-[#e3e3e3] p-3">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full h-10 rounded-lg text-left px-3 flex items-center gap-3 text-red-500 hover:bg-[#f1f1f1]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 17l5-5m0 0l-5-5m5 5H9m4 5v1a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
                                        <span class="text-[16px]">Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden lg:inline-flex items-center rounded-xl border border-[#ececec] px-4 h-10 text-[15px] font-medium text-[#111] no-underline hover:bg-[#fafafa]">Login</a>
                @endauth

                <a href="{{ route('sell-watch') }}" class="inline-flex items-center justify-center rounded-xl bg-[#d4b160] px-3.5 sm:px-5 h-9 sm:h-10 text-[14px] sm:text-[16px] font-semibold text-[#111] no-underline hover:bg-[#c7a552] whitespace-nowrap">
                    <span class="sm:hidden">Sell</span>
                    <span class="hidden sm:inline">Sell Watch</span>
                </a>
            </div>
        </div>
    </div>

    <div x-cloak x-show="mobileMenuOpen" class="lg:hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/45" @click="mobileMenuOpen = false"></div>

        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="-translate-x-full opacity-0"
            class="absolute inset-y-0 left-0 w-full max-w-[22rem] bg-white shadow-[0_24px_48px_rgba(0,0,0,0.18)] overflow-y-auto"
        >
            <div class="flex items-center justify-between px-4 py-4 border-b border-[#ececec]">
                <img src="{{ asset('images/logo.webp') }}" alt="Watch Market logo" class="h-8 w-auto max-w-[180px]">
                <button
                    type="button"
                    class="inline-flex w-10 h-10 rounded-xl border border-[#e5e5e5] items-center justify-center text-[#222] hover:bg-[#f6f6f6]"
                    @click="mobileMenuOpen = false"
                    aria-label="Close menu"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-4 py-5 space-y-5">
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('market.index') }}" class="rounded-2xl border border-[#e7e7e7] bg-[#fafafa] p-4 no-underline text-[#111]">
                        <div class="w-10 h-10 rounded-full bg-white border border-[#e5e5e5] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-[15px] font-semibold">Search Watches</div>
                    </a>

                    <a href="{{ auth()->check() ? route('messages.index') : route('login') }}" class="rounded-2xl border border-[#e7e7e7] bg-[#fafafa] p-4 no-underline text-[#111]">
                        <div class="w-10 h-10 rounded-full bg-white border border-[#e5e5e5] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8m-8 4h5m-9 5l3.6-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h1v3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-[15px] font-semibold">Messages</div>
                    </a>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('home') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 bg-[#f7f7f7] text-[15px] font-medium text-[#111] no-underline hover:bg-[#efefef]">
                        <span>Home</span>
                    </a>

                    <button
                        type="button"
                        class="w-full flex items-center justify-between rounded-2xl px-4 py-3 bg-[#f7f7f7] text-[15px] font-medium text-[#111] hover:bg-[#efefef]"
                        @click="mobileBrandsOpen = !mobileBrandsOpen"
                    >
                        <span>Buy a Watch</span>
                        <svg class="w-5 h-5 transition-transform" :class="mobileBrandsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-cloak x-show="mobileBrandsOpen" x-transition class="rounded-2xl bg-[#f6f4ee] border border-[#e7dec5] p-4">
                        <div class="text-[12px] uppercase tracking-[0.18em] text-[#8c7440] font-semibold">Popular Brands</div>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            @foreach($popularMenuBrands as $brand)
                                <a href="{{ route('market.index', ['brands' => [$brand->id]]) }}" class="rounded-xl bg-white px-3 py-2 text-[14px] font-medium text-[#222] no-underline border border-[#ece5d0] hover:border-[#d4b160]">
                                    {{ $brand->name }}
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('market.index') }}" class="mt-3 inline-flex text-[14px] font-semibold text-[#8c7440] no-underline hover:underline">
                            Browse all watches
                        </a>
                    </div>

                    <a href="{{ route('market.index') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 bg-[#f7f7f7] text-[15px] font-medium text-[#111] no-underline hover:bg-[#efefef]">
                        <span>All Watches</span>
                    </a>

                    <a href="{{ route('seller.trade.packages') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 bg-[#f7f7f7] text-[15px] font-medium text-[#111] no-underline hover:bg-[#efefef]">
                        <span>Pricing</span>
                    </a>

                    <a href="{{ route('sell-watch') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 bg-[#f7f7f7] text-[15px] font-medium text-[#111] no-underline hover:bg-[#efefef]">
                        <span>Sell A Watch</span>
                    </a>
                </nav>

                @auth
                    <div class="rounded-2xl border border-[#e7e7e7] bg-white overflow-hidden">
                        <div class="px-4 py-4 border-b border-[#ececec]">
                            <div class="text-[17px] font-semibold text-[#111]">{{ auth()->user()->name }}</div>
                            <div class="text-[14px] text-[#6a6a6a] break-all">{{ auth()->user()->email }}</div>
                        </div>

                        <div class="p-2">
                            <a href="{{ route('my-account') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 no-underline text-[#222] hover:bg-[#f3f3f3]">
                                <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4m-18 5l9 4 9-4"/></svg>
                                <span class="text-[15px]">Dashboard</span>
                            </a>
                            <a href="{{ route('my-account', ['tab' => 'profile']) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 no-underline text-[#222] hover:bg-[#f3f3f3]">
                                <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-[15px]">My Profile</span>
                            </a>
                            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 no-underline text-[#222] hover:bg-[#f3f3f3]">
                                <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8m-8 4h5m-9 5l3.6-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h1v3z"/></svg>
                                <span class="text-[15px]">Messages</span>
                            </a>
                            <a href="{{ route('my-account', ['tab' => 'settings']) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 no-underline text-[#222] hover:bg-[#f3f3f3]">
                                <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317a1 1 0 011.35-.936l.4.17a1 1 0 00.79 0l.4-.17a1 1 0 011.35.936l.035.433a1 1 0 00.497.786l.374.216a1 1 0 01.364 1.364l-.216.374a1 1 0 000 .79l.216.374a1 1 0 01-.364 1.364l-.374.216a1 1 0 00-.497.786l-.035.433a1 1 0 01-1.35.936l-.4-.17a1 1 0 00-.79 0l-.4.17a1 1 0 01-1.35-.936l-.035-.433a1 1 0 00-.497-.786l-.374-.216a1 1 0 01-.364-1.364l.216-.374a1 1 0 000-.79l-.216-.374a1 1 0 01.364-1.364l.374-.216a1 1 0 00.497-.786l.035-.433z"/><circle cx="12" cy="8.5" r="2.2" stroke-width="1.8"/></svg>
                                <span class="text-[15px]">Settings</span>
                            </a>
                        </div>

                        <div class="border-t border-[#ececec] p-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full h-11 rounded-xl text-left px-4 flex items-center gap-3 text-red-500 hover:bg-[#f7f7f7]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 17l5-5m0 0l-5-5m5 5H9m4 5v1a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
                                    <span class="text-[15px]">Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-[#e5e5e5] bg-white h-12 text-[15px] font-semibold text-[#111] no-underline hover:bg-[#fafafa]">
                        Login
                    </a>
                @endauth

                <a href="{{ route('sell-watch') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-[#d4b160] h-12 text-[15px] font-semibold text-[#111] no-underline hover:bg-[#c7a552]">
                    Sell Watch
                </a>
            </div>
        </div>
    </div>
</header>
