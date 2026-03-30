<x-main-layout>
    <div x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
        <section class="border-b border-[#e4e4e4] bg-[#f7f7f7]">
            <div class="site-container px-5 lg:px-8 py-4">
                @php
                    $brandChips = $selectedBrandChips ?? collect();
                    $baseQuery = request()->query();
                    $selectedBrandIds = (array) ($brandIds ?? []);
                    $selectedModelIds = (array) ($modelIds ?? []);
                    $selectedConditionIds = (array) ($conditionIds ?? []);
                    $activeFilterCount = 0;

                    if (filled($q ?? request('q'))) {
                        $activeFilterCount++;
                    }

                    if (filled(request('brand')) || ! empty($selectedBrandIds)) {
                        $activeFilterCount++;
                    }

                    if (! empty($selectedModelIds)) {
                        $activeFilterCount++;
                    }

                    if (! empty($selectedConditionIds)) {
                        $activeFilterCount++;
                    }

                    if (($sellerType ?? request('seller_type', 'all')) !== 'all') {
                        $activeFilterCount++;
                    }

                    if ((int) ($maxPrice ?? request('max_price', 100000)) !== 100000) {
                        $activeFilterCount++;
                    }

                    if ((int) ($distance ?? request('distance', 50)) !== 50) {
                        $activeFilterCount++;
                    }

                    if (request()->boolean('box_papers')) {
                        $activeFilterCount++;
                    }

                    unset($baseQuery['page']);
                @endphp

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <h1 class="text-[24px] md:text-[30px] font-semibold text-[#111]">{{ $adverts->total() }} Watches</h1>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <button
                            type="button"
                            class="lg:hidden inline-flex items-center justify-between gap-3 h-12 px-4 rounded-2xl border border-[#d7d7d7] bg-white text-[16px] font-semibold text-[#111]"
                            @click="filterOpen = true"
                        >
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6h18M6 12h12M10 18h4"/>
                                </svg>
                                <span>Filters</span>
                            </span>

                            @if($activeFilterCount > 0)
                                <span class="inline-flex min-w-7 h-7 rounded-full bg-black text-white text-[13px] font-semibold items-center justify-center px-2">
                                    {{ $activeFilterCount }}
                                </span>
                            @endif
                        </button>

                        <form method="GET" action="{{ route('market.index') }}" class="w-full sm:w-auto">
                            <input type="hidden" name="q" value="{{ $q ?? request('q') }}">
                            <input type="hidden" name="brand" value="{{ request('brand') }}">
                            @foreach((array) request('brands', []) as $brandId)
                                <input type="hidden" name="brands[]" value="{{ $brandId }}">
                            @endforeach
                            @foreach((array) request('models', []) as $modelId)
                                <input type="hidden" name="models[]" value="{{ $modelId }}">
                            @endforeach
                            @foreach((array) request('conditions', []) as $conditionId)
                                <input type="hidden" name="conditions[]" value="{{ $conditionId }}">
                            @endforeach
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            <input type="hidden" name="distance" value="{{ request('distance', $distance ?? 50) }}">
                            <input type="hidden" name="seller_type" value="{{ request('seller_type', $sellerType ?? 'all') }}">
                            @if(request()->boolean('box_papers'))
                                <input type="hidden" name="box_papers" value="1">
                            @endif

                            <div class="flex items-center gap-2 border border-[#d7d7d7] rounded-xl bg-white px-3 h-12 w-full sm:w-auto">
                                <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8M6 12h12M10 17h4"/></svg>
                                <select name="sort" onchange="this.form.submit()" class="border-0 bg-transparent text-[16px] focus:ring-0 focus:outline-none pr-8 w-full sm:w-auto">
                                    <option value="newest" {{ ($sort ?? request('sort', 'newest')) === 'newest' ? 'selected' : '' }}>Newest first</option>
                                    <option value="price_asc" {{ ($sort ?? request('sort')) === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_desc" {{ ($sort ?? request('sort')) === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                @if($brandChips->isNotEmpty())
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @foreach($brandChips as $chip)
                            @php
                                $removeQuery = $baseQuery;
                                if (($chip['type'] ?? '') === 'id') {
                                    $activeBrands = collect((array) ($removeQuery['brands'] ?? []))
                                        ->map(fn ($id) => (int) $id)
                                        ->reject(fn ($id) => $id === (int) ($chip['id'] ?? 0))
                                        ->values()
                                        ->all();
                                    if (empty($activeBrands)) {
                                        unset($removeQuery['brands']);
                                    } else {
                                        $removeQuery['brands'] = $activeBrands;
                                    }
                                } else {
                                    unset($removeQuery['brand']);
                                }
                            @endphp
                            <a href="{{ route('market.index', $removeQuery) }}"
                               class="inline-flex items-center gap-2 pl-4 pr-3 h-9 rounded-full bg-[#ececec] text-[16px] text-[#222] no-underline hover:bg-[#e2e2e2]">
                                <span>{{ $chip['label'] }}</span>
                                <span class="text-[#666]">&times;</span>
                            </a>
                        @endforeach
                        @php
                            $clearBrandsQuery = $baseQuery;
                            unset($clearBrandsQuery['brand'], $clearBrandsQuery['brands']);
                        @endphp
                        <a href="{{ route('market.index', $clearBrandsQuery) }}" class="text-[14px] text-[#666] no-underline hover:underline ml-1">Clear brands</a>
                    </div>
                @endif
            </div>
        </section>

        <section class="bg-[#f3f3f3]">
            <div class="site-container px-5 lg:px-8 py-8">
                <div x-cloak x-show="filterOpen" class="lg:hidden fixed inset-0 z-50">
                    <div class="absolute inset-0 bg-black/45" @click="filterOpen = false"></div>

                    <div
                        x-show="filterOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="-translate-x-full opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="-translate-x-full opacity-0"
                        class="absolute inset-y-0 left-0 w-full max-w-[24rem] overflow-y-auto p-4 sm:p-5"
                    >
                        @include('market.partials.filter-form', ['drawerMode' => true])
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-[320px_minmax(0,1fr)] gap-8">
                    <aside class="hidden lg:block">
                        @include('market.partials.filter-form', ['drawerMode' => false])
                    </aside>

                    <div>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-7">
                            @forelse($adverts as $advert)
                                <a href="{{ route('market.show', $advert) }}" class="group rounded-2xl overflow-hidden border border-[#d9d9d9] bg-[#f8f8f8] no-underline text-[#111] transition-all duration-200 hover:border-[#d4b160] hover:shadow-[0_14px_30px_rgba(0,0,0,0.12)] hover:-translate-y-1">
                                    <div class="relative h-72 bg-[#e9e9e9] overflow-hidden">
                                        @if($advert->mainImageUrl())
                                            <img src="{{ $advert->mainImageUrl() }}" alt="{{ $advert->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
                                        @endif
                                        @if($advert->is_featured)
                                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[12px] font-semibold bg-[#d4b160] text-black">Featured</span>
                                        @endif
                                    </div>
                                    <div class="px-4 py-4">
                                        <div class="text-[13px] uppercase text-[#c7a552] tracking-wide">{{ $advert->brand->name ?? 'Brand' }}</div>
                                        <div class="mt-1 text-[20px] leading-7 font-semibold">{{ \Illuminate\Support\Str::limit($advert->title, 32) }}</div>
                                        <div class="mt-1 text-[13px] text-[#5b5b5b]">Ref. #{{ $advert->id }}</div>
                                        <div class="mt-3 flex flex-wrap gap-2 text-[12px]">
                                            @if($advert->condition)<span class="px-2 py-1 rounded-full bg-[#ececec] text-[#454545]">{{ $advert->condition->name }}</span>@endif
                                            @if($advert->year)<span class="px-2 py-1 rounded-full bg-[#ececec] text-[#454545]">{{ $advert->year->name }}</span>@endif
                                            @if($advert->box && $advert->paper)<span class="px-2 py-1 rounded-full bg-[#ececec] text-[#454545]">Box &amp; Papers</span>@endif
                                        </div>
                                        <div class="my-4 h-px bg-[#dddddd]"></div>
                                        <div class="flex items-end gap-1">
                                            <div class="text-[20px] leading-none font-semibold">&pound;{{ number_format((float) $advert->price, 0) }}</div>
                                            <span class="text-[12px] text-[#666] mb-1">{{ $advert->price_negotiable ? 'ONO' : '' }}</span>
                                        </div>
                                        <div class="mt-3 text-[14px] text-[#666]">{{ $advert->user?->city ?: 'Location not set' }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="col-span-full rounded-2xl border border-[#d9d9d9] bg-[#f7f7f7] py-16 text-center text-[#666] text-[16px]">No watches found for selected filters.</div>
                            @endforelse
                        </div>

                        <div class="mt-8">
                            {{ $adverts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        (function () {
            const forms = Array.from(document.querySelectorAll('[data-market-filter-form]'));
            if (!forms.length) return;

            forms.forEach((form) => {
                const range = form.querySelector('[data-role="price-range"]');
                const rangeText = form.querySelector('[data-role="price-range-value"]');
                if (range && rangeText) {
                    const updateRange = () => {
                        const value = Number(range.value || 0).toLocaleString('en-GB');
                        rangeText.textContent = '£' + value;
                    };
                    range.addEventListener('input', updateRange);
                    updateRange();
                }

                const distanceRange = form.querySelector('[data-role="distance-range"]');
                const distanceText = form.querySelector('[data-role="distance-range-value"]');
                if (distanceRange && distanceText) {
                    const updateDistance = () => {
                        distanceText.textContent = `${distanceRange.value || 0} mi`;
                    };
                    distanceRange.addEventListener('input', updateDistance);
                    updateDistance();
                }

                const brandSearch = form.querySelector('[data-role="brand-search"]');
                const modelSearch = form.querySelector('[data-role="model-search"]');
                const brandItems = Array.from(form.querySelectorAll('.brand-filter-item'));
                const modelItems = Array.from(form.querySelectorAll('.model-filter-item'));
                const modelHint = form.querySelector('[data-role="model-select-hint"]');

                const selectedBrandIds = () => Array.from(form.querySelectorAll('input[name="brands[]"]:checked')).map((el) => el.value);

                const filterBrands = () => {
                    const needle = (brandSearch?.value || '').trim().toLowerCase();
                    brandItems.forEach((item) => {
                        const name = item.dataset.brandName || '';
                        item.style.display = !needle || name.includes(needle) ? '' : 'none';
                    });
                };

                const filterModels = () => {
                    const needle = (modelSearch?.value || '').trim().toLowerCase();
                    const selected = selectedBrandIds();
                    const hasParent = selected.length > 0;

                    if (modelHint) {
                        modelHint.style.display = hasParent ? 'none' : '';
                    }

                    modelItems.forEach((item) => {
                        const parent = item.dataset.parent || '';
                        const name = item.dataset.modelName || '';
                        const parentMatch = hasParent && selected.includes(parent);
                        const textMatch = !needle || name.includes(needle);
                        item.style.display = parentMatch && textMatch ? '' : 'none';
                    });
                };

                if (brandSearch) brandSearch.addEventListener('input', filterBrands);
                if (modelSearch) modelSearch.addEventListener('input', filterModels);
                filterBrands();
                filterModels();

                const sellerTypeChecks = form.querySelectorAll('.seller-type-check');
                sellerTypeChecks.forEach((el) => {
                    el.addEventListener('change', () => {
                        if (el.checked) {
                            sellerTypeChecks.forEach((other) => {
                                if (other !== el) other.checked = false;
                            });
                        } else {
                            const anyChecked = Array.from(sellerTypeChecks).some((checkbox) => checkbox.checked);
                            if (!anyChecked) {
                                const allOption = form.querySelector('.seller-type-check[value="all"]');
                                if (allOption) allOption.checked = true;
                            }
                        }
                        form.submit();
                    });
                });

                form.querySelectorAll('input[name="brands[]"]').forEach((el) => {
                    el.addEventListener('change', () => {
                        filterModels();
                        form.submit();
                    });
                });

                form.querySelectorAll('input[name="models[]"], input[name="conditions[]"], input[name="box_papers"]').forEach((el) => {
                    el.addEventListener('change', () => form.submit());
                });
            });
        })();
    </script>
</x-main-layout>
