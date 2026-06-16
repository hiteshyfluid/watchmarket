<x-main-layout>
    @php
        $packageData = $levels->map(function ($l) {
            $expLabel = $l->expirationLabel();
            $subtitle = $expLabel === '--' ? 'Until sold' : ucwords(str_replace('After ', '', $expLabel));
            return [
                'id'         => $l->id,
                'name'       => $l->name,
                'price'      => (float) ($l->initial_payment ?? 0),
                'subtitle'   => $subtitle,
                'min'        => $l->private_min_advert_price !== null ? (float) $l->private_min_advert_price : null,
                'max'        => $l->private_max_advert_price !== null ? (float) $l->private_max_advert_price : null,
                'features'   => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags((string) $l->description))))),
                'rangeLabel' => $l->privatePriceRangeLabel(),
            ];
        })->values();
    @endphp

    <div x-data="privatePricingPage()" class="min-h-screen bg-[#f7f7f7]">

        {{-- Price Range Filter --}}
        <div class="bg-white border-b border-[#e5e5e5] py-10">
            <div class="text-center">
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
        </div>

        {{-- Package Cards --}}
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-flash-messages class="mb-8" />

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <template x-for="pkg in filteredPackages" :key="pkg.id">
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
                                    <span x-show="pkg.price > 0">£<span x-text="pkg.price.toFixed(2)"></span></span>
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
                                <a href="{{ route('adverts.create') }}"
                                   class="inline-flex items-center justify-center w-full bg-black text-white font-bold py-3 text-[13px] uppercase tracking-widest hover:bg-[#222] transition no-underline">
                                    Create a Listing
                                </a>
                            </div>
                        </div>
                    </template>

                    <div x-show="filteredPackages.length === 0"
                         class="col-span-full py-16 text-center text-[#888] text-[16px]">
                        No packages available for this price range.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function privatePricingPage() {
            return {
                selectedPrice: 500,
                priceRanges: [
                    { label: '£0 - £999',     value: 500   },
                    { label: '£1000 - £4999', value: 2500  },
                    { label: '£5000 - £9999', value: 7500  },
                    { label: '£10000+',       value: 15000 },
                ],
                packages: @json($packageData),
                get filteredPackages() {
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
