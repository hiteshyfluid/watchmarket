@php
    $isDrawer = $drawerMode ?? false;
@endphp

<form
    data-market-filter-form
    method="GET"
    action="{{ route('market.index') }}"
    class="{{ $isDrawer ? 'bg-white border border-[#dbdbdb] rounded-[28px] p-5 sm:p-6 shadow-[0_22px_44px_rgba(0,0,0,0.12)] min-h-full' : 'bg-[#f5f5f5] border border-[#dbdbdb] rounded-2xl p-7 sticky top-6' }}"
>
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-[22px] font-semibold text-[#111]">Filters</h2>

        @if($isDrawer)
            <button
                type="button"
                class="inline-flex w-10 h-10 rounded-xl border border-[#e3e3e3] items-center justify-center text-[#222] hover:bg-[#f6f6f6]"
                @click="filterOpen = false"
                aria-label="Close filters"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12"/>
                </svg>
            </button>
        @endif
    </div>

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
        <input data-role="brand-search" type="text" placeholder="Search brands..." class="w-full h-10 border border-[#d7d7d7] rounded-lg px-3 text-[14px] mb-3 bg-white">
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
        <input data-role="model-search" type="text" placeholder="Search models..." class="w-full h-10 border border-[#d7d7d7] rounded-lg px-3 text-[14px] mb-3 bg-white">
        <p data-role="model-select-hint" class="text-[13px] text-[#777] mb-3">Select brand first to see models.</p>
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
        <input data-role="price-range" type="range" name="max_price" min="0" max="100000" step="500" value="{{ (int) ($maxPrice ?? request('max_price', 100000)) }}" class="w-full accent-black">
        <div class="flex items-center justify-between text-[14px] text-[#555] mt-2">
            <span>&pound;0</span>
            <span data-role="price-range-value">&pound;{{ number_format((int) ($maxPrice ?? request('max_price', 100000))) }}</span>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-[16px] font-semibold text-[#1b1b1b] mb-3">Distance</h3>
        <input data-role="distance-range" type="range" name="distance" min="0" max="250" step="5" value="{{ (int) ($distance ?? request('distance', 50)) }}" class="w-full accent-black">
        <div class="flex items-center justify-between text-[14px] text-[#555] mt-2">
            <span>0 mi</span>
            <span data-role="distance-range-value">{{ (int) ($distance ?? request('distance', 50)) }} mi</span>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-[#e1e1e1] space-y-3">
        <label class="flex items-center gap-3 text-[16px] text-[#444]">
            <input type="checkbox" name="box_papers" value="1" {{ request()->boolean('box_papers') ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
            <span>Box &amp; Papers</span>
        </label>
    </div>

    <div class="mt-8 flex items-center gap-2 {{ $isDrawer ? 'sticky bottom-0 bg-white pt-4 pb-1' : '' }}">
        <button type="submit" class="flex-1 h-11 rounded-xl bg-black text-white text-[16px] font-semibold">Apply</button>
        <a href="{{ route('market.index') }}" class="inline-flex items-center justify-center h-11 px-4 rounded-xl border border-[#d7d7d7] text-[16px] text-[#444] no-underline">Reset</a>
    </div>
</form>
