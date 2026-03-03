<x-main-layout>
    <section class="border-b border-[#e4e4e4] bg-[#f7f7f7]">
        <div class="site-container px-5 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <h1 class="text-[24px] md:text-[30px] font-semibold text-[#111]">{{ $adverts->total() }} Watches</h1>

                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('market.index') }}" class="flex items-center gap-2">
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

                        <div class="flex items-center gap-2 border border-[#d7d7d7] rounded-xl bg-white px-3 h-12">
                            <svg class="w-5 h-5 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8M6 12h12M10 17h4"/></svg>
                            <select name="sort" onchange="this.form.submit()" class="border-0 bg-transparent text-[16px] focus:ring-0 focus:outline-none pr-8">
                                <option value="newest" {{ ($sort ?? request('sort', 'newest')) === 'newest' ? 'selected' : '' }}>Newest first</option>
                                <option value="price_asc" {{ ($sort ?? request('sort')) === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ ($sort ?? request('sort')) === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            @php
                $brandChips = $selectedBrandChips ?? collect();
                $baseQuery = request()->query();
                unset($baseQuery['page']);
            @endphp
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
            <div class="grid grid-cols-1 lg:grid-cols-[320px_minmax(0,1fr)] gap-8">
                <aside>
                    <form id="market-filter-form" method="GET" action="{{ route('market.index') }}" class="bg-[#f5f5f5] border border-[#dbdbdb] rounded-2xl p-7 sticky top-6">
                        <h2 class="text-[22px] font-semibold text-[#111]">Filters</h2>

                        <div class="mt-6">
                            <label class="block text-[16px] font-semibold text-[#222] mb-2">Search</label>
                            <div class="h-11 rounded-xl border border-[#d7d7d7] bg-[#f7f7f7] px-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" name="q" value="{{ $q ?? request('q') }}" placeholder="Brand, model, reference.." class="w-full bg-transparent border-0 focus:ring-0 text-[16px] text-[#333] placeholder:text-[#999]">
                            </div>
                        </div>

                        <input type="hidden" name="brand" value="{{ request('brand') }}">
                        <input type="hidden" name="sort" value="{{ $sort ?? request('sort', 'newest') }}">

                        <div class="mt-8">
                            <h3 class="text-[16px] font-semibold text-[#1b1b1b] mb-3">Brand</h3>
                            <input id="brandFilterSearch" type="text" placeholder="Search brands..." class="w-full h-10 border border-[#d7d7d7] rounded-lg px-3 text-[14px] mb-3 bg-white">
                            <div class="max-h-72 overflow-y-auto pr-1 space-y-3">
                                @php
                                    $popularBrands = $brands->where('is_popular', true)->values();
                                    $otherBrands = $brands->where('is_popular', false)->values();
                                @endphp

                                @if($popularBrands->isNotEmpty())
                                    <div class="text-[12px] font-semibold text-[#777] uppercase">Popular</div>
                                    @foreach($popularBrands as $brand)
                                        <label class="brand-filter-item flex items-center gap-3 text-[16px] text-[#444]" data-brand-name="{{ strtolower($brand->name) }}">
                                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}" {{ in_array($brand->id, $brandIds ?? []) ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                            <span>{{ $brand->name }}</span>
                                        </label>
                                    @endforeach
                                @endif

                                @if($otherBrands->isNotEmpty())
                                    <div class="text-[12px] font-semibold text-[#777] uppercase pt-1">Other Brands</div>
                                    @foreach($otherBrands as $brand)
                                        <label class="brand-filter-item flex items-center gap-3 text-[16px] text-[#444]" data-brand-name="{{ strtolower($brand->name) }}">
                                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}" {{ in_array($brand->id, $brandIds ?? []) ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                            <span>{{ $brand->name }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-[16px] font-semibold text-[#1b1b1b] mb-3">Model</h3>
                            <input id="modelFilterSearch" type="text" placeholder="Search models..." class="w-full h-10 border border-[#d7d7d7] rounded-lg px-3 text-[14px] mb-3 bg-white">
                            <p id="modelSelectHint" class="text-[13px] text-[#777] mb-3">Select brand first to see models.</p>
                            <div class="max-h-72 overflow-y-auto pr-1 space-y-3">
                                @foreach($models as $model)
                                    <label class="model-filter-item flex items-center gap-3 text-[16px] text-[#444]" data-parent="{{ $model->parent_id }}" data-model-name="{{ strtolower($model->name) }}">
                                        <input type="checkbox" name="models[]" value="{{ $model->id }}" {{ in_array($model->id, $modelIds ?? []) ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                        <span>{{ $model->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-[16px] font-semibold text-[#1b1b1b] mb-3">Seller Type</h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 text-[16px] text-[#444]">
                                    <input type="checkbox" name="seller_type" value="all" {{ ($sellerType ?? request('seller_type', 'all')) === 'all' ? 'checked' : '' }} class="seller-type-check w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                    <span>All</span>
                                </label>
                                <label class="flex items-center gap-3 text-[16px] text-[#444]">
                                    <input type="checkbox" name="seller_type" value="trade" {{ ($sellerType ?? request('seller_type')) === 'trade' ? 'checked' : '' }} class="seller-type-check w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                    <span>Trade Seller</span>
                                </label>
                                <label class="flex items-center gap-3 text-[16px] text-[#444]">
                                    <input type="checkbox" name="seller_type" value="private" {{ ($sellerType ?? request('seller_type')) === 'private' ? 'checked' : '' }} class="seller-type-check w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                    <span>Private Seller</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-[16px] font-semibold text-[#1b1b1b] mb-3">Condition</h3>
                            <div class="space-y-3">
                                @foreach($conditions as $condition)
                                    <label class="flex items-center gap-3 text-[16px] text-[#444]">
                                        <input type="checkbox" name="conditions[]" value="{{ $condition->id }}" {{ in_array($condition->id, $conditionIds ?? []) ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                        <span>{{ $condition->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-[16px] font-semibold text-[#1b1b1b] mb-3">Price Range</h3>
                            <input id="priceRange" type="range" name="max_price" min="0" max="100000" step="500" value="{{ (int) ($maxPrice ?? request('max_price', 100000)) }}" class="w-full accent-black">
                            <div class="flex items-center justify-between text-[14px] text-[#555] mt-2">
                                <span>&pound;0</span>
                                <span id="priceRangeValue">&pound;{{ number_format((int) ($maxPrice ?? request('max_price', 100000))) }}</span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-[16px] font-semibold text-[#1b1b1b] mb-3">Distance</h3>
                            <input id="distanceRange" type="range" name="distance" min="0" max="250" step="5" value="{{ (int) ($distance ?? request('distance', 50)) }}" class="w-full accent-black">
                            <div class="flex items-center justify-between text-[14px] text-[#555] mt-2">
                                <span>0 mi</span>
                                <span id="distanceRangeValue">{{ (int) ($distance ?? request('distance', 50)) }} mi</span>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-[#e1e1e1] space-y-3">
                            <label class="flex items-center gap-3 text-[16px] text-[#444]">
                                <input type="checkbox" name="box_papers" value="1" {{ request()->boolean('box_papers') ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                <span>Box &amp; Papers</span>
                            </label>
                        </div>

                        <div class="mt-8 flex items-center gap-2">
                            <button type="submit" class="flex-1 h-11 rounded-xl bg-black text-white text-[16px] font-semibold">Apply</button>
                            <a href="{{ route('market.index') }}" class="inline-flex items-center justify-center h-11 px-4 rounded-xl border border-[#d7d7d7] text-[16px] text-[#444] no-underline">Reset</a>
                        </div>
                    </form>
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

    <script>
        (function () {
            const form = document.getElementById('market-filter-form');
            if (!form) return;

            const range = document.getElementById('priceRange');
            const rangeText = document.getElementById('priceRangeValue');
            if (range && rangeText) {
                const updateRange = () => {
                    const value = Number(range.value || 0).toLocaleString('en-GB');
                    rangeText.textContent = '£' + value;
                };
                range.addEventListener('input', updateRange);
                updateRange();
            }

            const distanceRange = document.getElementById('distanceRange');
            const distanceText = document.getElementById('distanceRangeValue');
            if (distanceRange && distanceText) {
                const updateDistance = () => {
                    distanceText.textContent = `${distanceRange.value || 0} mi`;
                };
                distanceRange.addEventListener('input', updateDistance);
                updateDistance();
            }

            const brandSearch = document.getElementById('brandFilterSearch');
            const modelSearch = document.getElementById('modelFilterSearch');
            const brandItems = Array.from(form.querySelectorAll('.brand-filter-item'));
            const modelItems = Array.from(form.querySelectorAll('.model-filter-item'));
            const modelHint = document.getElementById('modelSelectHint');

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
                if (modelHint) modelHint.style.display = hasParent ? 'none' : '';

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
        })();
    </script>
</x-main-layout>
