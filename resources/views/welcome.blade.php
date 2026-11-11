<x-main-layout>
    @push('structured-data')
        @include('partials.json-ld', ['data' => \App\Support\StructuredData::website()])
    @endpush

    <section class="relative overflow-hidden text-white">
        <div class="absolute inset-0 bg-center bg-cover" style="background-image:url('{{ asset('images/banner.jpg') }}');"></div>
        <div class="absolute inset-0 bg-black/75"></div>

        <div class="relative site-container px-4 lg:px-8 py-16 sm:py-20 lg:py-28">
            <div class="max-w-[860px]">
                <h1 class="max-w-[680px] text-[34px] sm:text-[44px] md:text-[60px] font-medium leading-[1.02] sm:leading-[1.08] tracking-[-0.03em]">
                    The Trusted Marketplace for
                    <span class="block text-[#d4b160] font-semibold">Luxury Timepieces</span>
                </h1>

                <p class="mt-5 sm:mt-7 text-[15px] sm:text-[16px] text-white/90 leading-[1.65] max-w-[620px]">
                    Buy and sell pre-owned luxury watches from verified sellers. Connect directly,
                    meet safely, transact with confidence.
                </p>

                <form action="{{ route('market.index') }}" method="GET" class="mt-8 sm:mt-10 flex flex-col sm:flex-row gap-3 sm:gap-4 w-full max-w-[680px]">
                    <label class="flex-1 min-h-[56px] rounded-[20px] border border-white/30 bg-white/10 backdrop-blur px-4 py-3 flex items-center gap-3 shadow-[0_18px_45px_rgba(0,0,0,0.18)]">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white/80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            name="q"
                            placeholder="Search by brand, model, or reference..."
                            class="w-full bg-transparent border-0 outline-none ring-0 focus:ring-0 focus:outline-none text-white placeholder:text-white/60 text-[15px] sm:text-[16px] leading-tight"
                        >
                    </label>

                    <button type="submit" class="w-full sm:w-44 min-h-[56px] rounded-[20px] bg-[#d4b160] text-[#111] text-[16px] font-semibold hover:bg-[#c7a552] transition shadow-[0_18px_45px_rgba(0,0,0,0.2)]">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="bg-[#ffffff] border-y border-[#e5e5e5]">
        <div class="site-container px-5 lg:px-8 py-9">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex items-center gap-4">
                    <span class="w-14 h-14 rounded-full bg-black flex items-center justify-center text-[#d4b160]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-[18px] font-semibold text-[#111] leading-tight">Verified Sellers</h3>
                        <p class="text-[14px] text-[#5d5d5d] mt-1">ID &amp; dealer verification available</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="w-14 h-14 rounded-full bg-black flex items-center justify-center text-[#d4b160]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8m-8 4h5m-9 5l3.6-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h1v3z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-[18px] font-semibold text-[#111] leading-tight">Direct Communication</h3>
                        <p class="text-[14px] text-[#5d5d5d] mt-1">Message sellers securely</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="w-14 h-14 rounded-full bg-black flex items-center justify-center text-[#d4b160]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z"/>
                            <circle cx="12" cy="10" r="2.2" stroke-width="1.8"></circle>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-[18px] font-semibold text-[#111] leading-tight">Quickly Buy and Sell your Watch</h3>
                        <p class="text-[14px] text-[#5d5d5d] mt-1">No commission charges</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-[#fafaf9] mt-2 py-12">
        <div class="site-container px-5 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
                <h2 class="text-[28px] sm:text-[30px] font-semibold text-[#111]">Browse by Brand</h2>
                <a href="{{ route('market.index') }}" class="text-[#c7a552] text-[14px] font-medium no-underline hover:underline">View all brands &nbsp;&rsaquo;</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @forelse($featuredBrands as $brand)
                    <a href="{{ route('market.index', ['q' => $brand->name]) }}"
                       class="rounded-2xl border border-[#d9d9d9] bg-[#f6f6f6] hover:border-[#d4b160] transition no-underline text-[#111] p-4 flex flex-col items-center justify-center min-h-[170px]">
                        <div class="w-20 h-20 rounded-full bg-[#e5e5e5] overflow-hidden flex items-center justify-center">
                            @if($brand->imageUrl())
                                <img src="{{ $brand->imageUrl() }}" alt="{{ $brand->name }}" class="w-full h-full object-contain">
                            @else
                                <span class="text-[14px] font-semibold text-[#777]">{{ strtoupper(substr($brand->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="mt-4 text-[14px] leading-5 font-medium text-center">{{ $brand->name }}</div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-[#d7ddec] bg-[#f7f9ff] py-10 text-center text-[#61739e] text-[16px]">
                        No featured brands configured yet.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <section class="bg-[#080808] py-14">
        <div class="site-container px-5 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-7">
                <h2 class="text-[28px] sm:text-[30px] font-semibold text-white">Featured Watches</h2>
                <a href="{{ route('market.index') }}" class="text-[#d4b160] text-[14px] font-medium no-underline hover:underline">View all &nbsp;&rsaquo;</a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 lg:gap-6">
                @forelse($featuredAdverts as $advert)
                    <a href="{{ route('market.show', $advert) }}" class="rounded-xl sm:rounded-2xl overflow-hidden border border-[#2a2a2a] bg-[#141211] no-underline text-white">
                        <div class="relative h-32 sm:h-56 lg:h-72 bg-[#1f1f1f]">
                            @if($advert->mainImageUrl())
                                <img src="{{ $advert->mainImageUrl() }}" alt="{{ $advert->title }}" class="w-full h-full object-cover">
                            @endif
                            <span class="absolute top-1.5 left-1.5 sm:top-3 sm:left-3 px-1.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[12px] font-semibold bg-[#d4b160] text-black">Featured</span>
                        </div>
                        <div class="px-2.5 py-2.5 sm:px-4 sm:py-4">
                            <div class="text-[10px] sm:text-[13px] uppercase text-[#d4b160] tracking-wide">{{ $advert->brand->name ?? 'Brand' }}</div>
                            <div class="mt-0.5 sm:mt-1 text-[13px] sm:text-[20px] leading-4 sm:leading-9 font-semibold truncate">{{ \Illuminate\Support\Str::limit($advert->title, 28) }}</div>
                            @if($advert->reference_number)<div class="mt-0.5 sm:mt-1 text-[10px] sm:text-[13px] text-white/70">Reference Number: {{ $advert->reference_number }}</div>@endif
                            <div class="mt-1.5 sm:mt-3 flex flex-wrap gap-1 sm:gap-2 text-[9px] sm:text-[12px]">
                                @if($advert->condition)<span class="px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full bg-[#2b2b2b]">Condition: {{ $advert->condition->name }}</span>@endif
                                @if($advert->year)<span class="px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full bg-[#2b2b2b]">{{ $advert->year->name }}</span>@endif
                                <span class="px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full bg-[#2b2b2b]">{{ $advert->boxAndPapersLabel() }}</span>
                            </div>
                            <div class="my-2 sm:my-4 h-px bg-white/20"></div>
                            <div class="text-[14px] sm:text-[20px] font-semibold leading-none">&pound;{{ number_format((float) $advert->price, 0) }}</div>
                            <div class="mt-1 sm:mt-3 text-[11px] sm:text-[14px] text-white/70 flex items-center gap-1 sm:gap-1.5 truncate">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-[#7a7a7a] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z"/><circle cx="12" cy="10" r="2.2" stroke-width="1.8"></circle></svg>
                                <span class="truncate">{{ $advert->locationLabel() }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-[#2a2a2a] bg-[#141211] py-14 text-center text-white/70 text-[16px]">
                        No featured watches yet. Mark adverts as featured from admin.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-[#fafaf9] py-14">
        <div class="site-container px-5 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-7">
                <h2 class="text-[28px] sm:text-[30px] font-semibold text-[#111]">Recently Listed</h2>
                <a href="{{ route('market.index') }}" class="text-[#c7a552] text-[14px] font-medium no-underline hover:underline">View all &nbsp;&rsaquo;</a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 lg:gap-6">
                @forelse($recentAdverts as $advert)
                    <a href="{{ route('market.show', $advert) }}" class="group rounded-xl sm:rounded-2xl overflow-hidden border border-[#d9d9d9] bg-[#f8f8f8] no-underline text-[#111] transition-all duration-200 hover:border-[#d4b160] hover:shadow-[0_14px_30px_rgba(0,0,0,0.12)] hover:-translate-y-1">
                        <div class="relative h-32 sm:h-56 lg:h-72 bg-[#e9e9e9] overflow-hidden">
                            @if($advert->mainImageUrl())
                                <img src="{{ $advert->mainImageUrl() }}" alt="{{ $advert->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
                            @endif
                            @if($advert->is_featured)
                                <span class="absolute top-1.5 left-1.5 sm:top-3 sm:left-3 px-1.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[12px] font-semibold bg-[#d4b160] text-black">Featured</span>
                            @endif
                            <span class="absolute top-1.5 right-1.5 sm:top-3 sm:right-3 w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-white/95 border border-[#ddd] flex items-center justify-center text-[#595959]">
                                <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </span>
                        </div>
                        <div class="px-2.5 py-2.5 sm:px-4 sm:py-4">
                            <div class="text-[10px] sm:text-[13px] uppercase text-[#c7a552] tracking-wide">{{ $advert->brand->name ?? 'Brand' }}</div>
                            <div class="mt-0.5 sm:mt-1 text-[13px] sm:text-[20px] leading-4 sm:leading-7 font-semibold truncate">{{ \Illuminate\Support\Str::limit($advert->title, 30) }}</div>
                            @if($advert->reference_number)<div class="mt-0.5 sm:mt-1 text-[10px] sm:text-[13px] text-[#5b5b5b]">Reference Number: {{ $advert->reference_number }}</div>@endif
                            <div class="mt-1.5 sm:mt-3 flex flex-wrap gap-1 sm:gap-2 text-[9px] sm:text-[12px]">
                                @if($advert->condition)<span class="px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full bg-[#ececec] text-[#454545]">Condition: {{ $advert->condition->name }}</span>@endif
                                @if($advert->year)<span class="px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full bg-[#ececec] text-[#454545]">{{ $advert->year->name }}</span>@endif
                                <span class="px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full bg-[#ececec] text-[#454545]">{{ $advert->boxAndPapersLabel() }}</span>
                            </div>
                            <div class="my-2 sm:my-4 h-px bg-[#dddddd]"></div>
                            <div class="flex items-end gap-1">
                                <div class="text-[14px] sm:text-[20px] leading-none font-semibold">&pound;{{ number_format((float) $advert->price, 0) }}</div>
                                <span class="text-[10px] sm:text-[12px] text-[#666] mb-0.5 sm:mb-1">ONO</span>
                            </div>
                            <div class="mt-1 sm:mt-3 text-[11px] sm:text-[14px] text-[#666] flex items-center gap-1 sm:gap-1.5 truncate">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-[#7a7a7a] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z"/><circle cx="12" cy="10" r="2.2" stroke-width="1.8"></circle></svg>
                                <span class="truncate">{{ $advert->locationLabel() }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-[#d9d9d9] bg-[#f7f7f7] py-14 text-center text-[#666] text-[16px]">
                        No recent adverts available.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-[#f3f3f3] py-20 border-t border-[#e5e5e5]">
        <div class="site-container px-5 lg:px-8 text-center">
            <h2 class="text-[30px] sm:text-[32px] font-semibold text-[#111]">Ready to Sell Your Watch?</h2>
            <p class="mt-5 text-[16px] text-[#4d4d4d] max-w-3xl mx-auto">
                List your timepiece in minutes. Reach thousands of verified buyers across the UK.
            </p>

            <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('sell-watch') }}"
                   class="inline-flex items-center justify-center w-full sm:w-auto min-w-[200px] h-14 px-8 rounded-2xl bg-black text-white text-[16px] font-semibold no-underline hover:bg-[#161616] transition">
                    Create a Listing
                </a>
                <a href="{{ route('seller.pricing') }}"
                   class="inline-flex items-center justify-center w-full sm:w-auto min-w-[180px] h-14 px-8 rounded-2xl border border-[#d2d2d2] bg-white text-[#111] text-[16px] font-medium no-underline hover:bg-[#fafafa] transition">
                    View Pricing
                </a>
            </div>
        </div>
    </section>


</x-main-layout>
