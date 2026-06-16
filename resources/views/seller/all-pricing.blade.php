<x-main-layout>
    @php
        $privatePackageData = $privateLevels->map(function ($l) {
            $expLabel = $l->expirationLabel();
            $subtitle = $expLabel === '--' ? 'Until sold' : ucwords(str_replace('After ', '', $expLabel));
            return [
                'id'       => $l->id,
                'name'     => $l->name,
                'price'    => (float) ($l->initial_payment ?? 0),
                'subtitle' => $subtitle,
                'min'      => $l->private_min_advert_price !== null ? (float) $l->private_min_advert_price : null,
                'max'      => $l->private_max_advert_price !== null ? (float) $l->private_max_advert_price : null,
                'features' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags((string) $l->description))))),
                'rangeLabel' => $l->privatePriceRangeLabel(),
            ];
        })->values();
    @endphp

    <div x-data="pricingPage()" class="min-h-screen bg-[#f7f7f7]">

        {{-- Tab Switcher --}}
        <div class="bg-white border-b border-[#e5e5e5] py-10">
            <div class="flex justify-center">
                <div class="relative inline-flex flex-col items-stretch">
                    {{-- Buttons – no overflow-hidden so the pointer can break out --}}
                    <div class="flex border border-[#ccc] rounded-lg shadow-sm">
                        <button
                            @click="tab = 'private'"
                            :class="tab === 'private' ? 'bg-[#111] text-white' : 'bg-[#f5f5f5] text-[#555] hover:bg-[#ebebeb]'"
                            class="px-10 py-3 text-[15px] font-semibold rounded-l-lg transition-colors duration-150 focus:outline-none">
                            Private Seller
                        </button>
                        <button
                            @click="tab = 'trade'"
                            :class="tab === 'trade' ? 'bg-[#111] text-white' : 'bg-[#f5f5f5] text-[#555] hover:bg-[#ebebeb]'"
                            class="px-10 py-3 text-[15px] font-semibold rounded-r-lg transition-colors duration-150 border-l border-[#ccc] focus:outline-none">
                            Trade Seller
                        </button>
                    </div>
                    {{-- Pointer triangle – sits just below the tab container --}}
                    <div class="absolute left-0 right-0 top-full -mt-px flex pointer-events-none">
                        <div class="w-1/2 flex justify-center">
                            <span x-show="tab === 'private'" x-cloak class="block"
                                  style="width:0;height:0;border-left:9px solid transparent;border-right:9px solid transparent;border-top:10px solid #111;"></span>
                        </div>
                        <div class="w-1/2 flex justify-center">
                            <span x-show="tab === 'trade'" x-cloak class="block"
                                  style="width:0;height:0;border-left:9px solid transparent;border-right:9px solid transparent;border-top:10px solid #111;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Private Seller Section --}}
        <div x-show="tab === 'private'" x-cloak class="py-12">
            {{-- Price Range Filter --}}
            <div class="text-center mb-10">
                <p class="text-[18px] font-semibold text-[#111] mb-4">How much are you selling your Watch for?</p>
                <div class="relative inline-block">
                    <select x-model="selectedPrice"
                            class="appearance-none border border-[#d0d0d0] bg-white rounded px-6 py-3 pr-10 text-[15px] min-w-[220px] cursor-pointer focus:outline-none focus:ring-2 focus:ring-black">
                        <template x-for="range in priceRanges" :key="range.value">
                            <option :value="range.value" x-text="range.label"></option>
                        </template>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                        <svg class="w-4 h-4 text-[#555]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Private Package Cards --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <template x-for="pkg in filteredPrivatePackages" :key="pkg.id">
                        <div class="bg-white border border-[#e0e0e0] rounded overflow-hidden flex flex-col shadow-sm hover:shadow-md transition">
                            {{-- Card Header --}}
                            <div class="bg-[#f0f0f0] px-5 py-5 text-center border-b border-[#e0e0e0]">
                                <h3 class="text-[22px] font-bold text-[#111]" x-text="pkg.name"></h3>
                                <p class="text-[13px] text-[#777] mt-1" x-text="pkg.subtitle"></p>
                            </div>

                            {{-- Price --}}
                            <div class="px-5 py-6 text-center border-b border-[#e0e0e0]">
                                <p class="text-[40px] font-bold text-[#111] leading-none">
                                    <span x-show="pkg.price === 0">FREE</span>
                                    <span x-show="pkg.price > 0">£<span x-text="pkg.price % 1 === 0 ? pkg.price.toFixed(2) : pkg.price.toFixed(2)"></span></span>
                                </p>
                            </div>

                            {{-- Features --}}
                            <div class="flex-1 divide-y divide-[#efefef]">
                                <template x-for="(feature, i) in pkg.features" :key="i">
                                    <div class="px-5 py-3 text-[13px] text-[#555]" x-text="feature"></div>
                                </template>
                                <template x-if="pkg.rangeLabel">
                                    <div class="px-5 py-3 text-[13px] text-[#555]" x-text="pkg.rangeLabel"></div>
                                </template>
                            </div>

                            {{-- CTA --}}
                            <div class="px-5 py-5 border-t border-[#efefef]">
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center w-full bg-black text-white font-bold py-3 text-[13px] uppercase tracking-widest hover:bg-[#222] transition no-underline">
                                    Get Started
                                </a>
                            </div>
                        </div>
                    </template>

                    <div x-show="filteredPrivatePackages.length === 0"
                         class="col-span-full py-16 text-center text-[#888] text-[16px]">
                        No packages available for this price range.
                    </div>
                </div>
            </div>
        </div>

        {{-- Trade Seller Section --}}
        <div x-show="tab === 'trade'" x-cloak class="py-12">
            @if($tradeLevels->where('has_trial', true)->where('trial_amount', '0.00')->isNotEmpty())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                <div class="bg-[#d4b160] text-[#111] text-center py-4 px-6 rounded-lg font-semibold text-[17px] tracking-wide">
                    Select Trade Subscription – Get 3 Months Free
                </div>
            </div>
            @endif

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @forelse($tradeLevels as $level)
                    <div class="bg-white border border-[#e0e0e0] rounded overflow-hidden flex flex-col shadow-sm hover:shadow-md transition">
                        {{-- Card Header --}}
                        <div class="bg-[#f0f0f0] px-5 py-5 text-center border-b border-[#e0e0e0]">
                            <h3 class="text-[22px] font-bold text-[#111] uppercase">{{ $level->name }}</h3>
                            <p class="text-[13px] text-[#777] mt-1">{{ $level->has_recurring ? 'Monthly subscription' : 'One-time payment' }}</p>
                        </div>

                        {{-- Price --}}
                        <div class="px-5 py-6 text-center border-b border-[#e0e0e0]">
                            <p class="text-[40px] font-bold text-[#111] leading-none">
                                £{{ number_format((float) ($level->billing_amount ?? $level->initial_payment ?? 0), 2) }}
                                @if($level->has_recurring)
                                    <span class="text-[18px] font-normal text-[#2563eb]">/ Month</span>
                                @endif
                            </p>
                        </div>

                        {{-- Features --}}
                        <div class="flex-1 divide-y divide-[#efefef]">
                            @foreach(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags((string) $level->description)))) as $index => $line)
                            <div class="px-5 py-3 text-[13px] {{ $index === 0 ? 'text-[#c0392b]' : 'text-[#555]' }}">{{ $line }}</div>
                            @endforeach
                            @if($level->tradeAdvertLimitLabel())
                            <div class="px-5 py-3 text-[13px] text-[#555]">{{ $level->tradeAdvertLimitLabel() }}</div>
                            @endif
                        </div>

                        {{-- CTA --}}
                        <div class="px-5 py-5 border-t border-[#efefef]">
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center w-full bg-black text-white font-bold py-3 text-[13px] uppercase tracking-widest hover:bg-[#222] transition no-underline">
                                Get Started
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center text-[#888] text-[16px]">No trade packages available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function pricingPage() {
            return {
                tab: 'private',
                selectedPrice: 500,
                priceRanges: [
                    { label: '£0 - £999',     value: 500   },
                    { label: '£1000 - £4999', value: 2500  },
                    { label: '£5000 - £9999', value: 7500  },
                    { label: '£10000+',       value: 15000 },
                ],
                packages: @json($privatePackageData),
                get filteredPrivatePackages() {
                    const price = parseFloat(this.selectedPrice);
                    return this.packages.filter(pkg => {
                        const minOk = pkg.min === null || price >= pkg.min;
                        const maxOk = pkg.max === null || price <= pkg.max;
                        return minOk && maxOk;
                    });
                },
            };
        }
    </script>
</x-main-layout>
