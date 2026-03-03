<x-main-layout>
    @php
        $galleryImages = [];
        if ($advert->mainImageUrl()) {
            $galleryImages[] = $advert->mainImageUrl();
        }
        foreach ($advert->images as $img) {
            $galleryImages[] = asset('storage/' . $img->image_path);
        }
        $imageCount = count($galleryImages);
    @endphp

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <section
        class="max-w-[1280px] mx-auto px-4 lg:px-6 py-8"
        x-data="watchShowPage({
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            loginUrl: '{{ route('login') }}',
            enquireUrl: '{{ route('messages.enquire', $advert) }}',
            messagesUrl: '{{ route('messages.index') }}'
        })"
    >
        <div class="text-[14px] text-[#777] mb-5">
            <a href="{{ route('home') }}" class="text-[#777] hover:text-black no-underline">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('market.index') }}" class="text-[#777] hover:text-black no-underline">Watches</a>
            <span class="mx-1">/</span>
            <span>{{ $advert->brand->name ?? 'Watch' }}</span>
            <span class="mx-1">/</span>
            <span class="text-[#222]">{{ $advert->title }}</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[560px_1fr] gap-10">
            <div>
                <div class="relative bg-[#f8f8f8] rounded-2xl border border-[#e8e8e8] overflow-hidden">
                    <div class="swiper watch-main-swiper">
                        <div class="swiper-wrapper">
                            @forelse($galleryImages as $image)
                                <div class="swiper-slide">
                                    <img src="{{ $image }}" alt="{{ $advert->title }}" class="w-full h-[560px] object-cover">
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <div class="w-full h-[560px] flex items-center justify-center text-[#aaa]">No image</div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if($imageCount > 1)
                        <button type="button" class="watch-main-prev absolute left-3 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full border border-[#ddd] bg-white text-[#222] text-lg">&#8249;</button>
                        <button type="button" class="watch-main-next absolute right-3 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full border border-[#ddd] bg-white text-[#222] text-lg">&#8250;</button>
                    @endif
                </div>

                @if($imageCount > 1)
                    <div class="mt-3 px-1">
                        <div class="swiper watch-thumb-swiper">
                            <div class="swiper-wrapper">
                                @foreach($galleryImages as $idx => $image)
                                    <div class="swiper-slide">
                                        <div class="watch-thumb-item h-[72px] w-[72px] rounded-xl overflow-hidden border border-[#e1e1e1] cursor-pointer bg-white">
                                            <img src="{{ $image }}" alt="Thumb {{ $idx + 1 }}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <p class="text-[13px] uppercase tracking-[0.12em] text-[#c8a95a] font-semibold mb-1">{{ $advert->brand->name ?? '-' }}</p>
                <h1 class="text-[28px] md:text-[34px] font-semibold leading-[1.1] text-[#111]">{{ $advert->title }}</h1>

                @if($advert->reference_number)
                    <p class="text-[14px] text-[#6b7280] mt-2">Ref. {{ $advert->reference_number }}</p>
                @endif

                <div class="mt-3 flex items-end gap-2">
                    <div class="text-[28px] md:text-[30px] leading-none font-semibold text-[#111]">&pound;{{ number_format((float) $advert->price, 0) }}</div>
                    <span class="text-[12px] text-[#666] pb-1">{{ $advert->price_negotiable ? 'ONO' : '' }}</span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @if($advert->condition?->name)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[13px] bg-[#e9f8ee] text-[#1f7a3e]">{{ $advert->condition->name }}</span>
                    @endif
                    @if($advert->year?->name)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[13px] bg-[#f3f4f6] text-[#333]">{{ $advert->year->name }}</span>
                    @endif
                    @if($advert->accept_traders)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[13px] border border-[#22c55e] text-[#1f7a3e]">Trades Welcome</span>
                    @endif
                </div>

                <div class="mt-5">
                    <button type="button" @click="openEnquiry()" class="w-full h-[54px] bg-black text-white rounded-xl font-semibold text-[16px] hover:bg-[#1f1f1f]">Enquire About This Watch</button>
                </div>

                <div class="mt-7 border-t border-[#e8e8e8] pt-6">
                    <h2 class="text-[31px] font-semibold text-[#111] mb-4">Specifications</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-[16px]">
                        <div><span class="text-[#777] text-[14px]">Case Size</span><div class="text-[#111] font-medium mt-0.5">{{ $advert->case_size_mm ? $advert->case_size_mm.'mm' : '-' }}</div></div>
                        <div><span class="text-[#777] text-[14px]">Case Material</span><div class="text-[#111] font-medium mt-0.5">{{ $advert->caseMaterial->name ?? '-' }}</div></div>
                        <div><span class="text-[#777] text-[14px]">Movement</span><div class="text-[#111] font-medium mt-0.5">{{ $advert->movement->name ?? '-' }}</div></div>
                        <div><span class="text-[#777] text-[14px]">Dial Colour</span><div class="text-[#111] font-medium mt-0.5">{{ $advert->dialColour->name ?? '-' }}</div></div>
                        <div><span class="text-[#777] text-[14px]">Box &amp; Papers</span><div class="text-[#111] font-medium mt-0.5">{{ ($advert->box->name ?? 'No Box') . ' / ' . ($advert->paper->name ?? 'No Papers') }}</div></div>
                        <div><span class="text-[#777] text-[14px]">Year</span><div class="text-[#111] font-medium mt-0.5">{{ $advert->year->name ?? '-' }}</div></div>
                    </div>
                </div>

                <div class="mt-7 border-t border-[#e8e8e8] pt-6">
                    <h2 class="text-[31px] font-semibold text-[#111] mb-3">Description</h2>
                    <div class="text-[15px] leading-7 text-[#404040]">{!! nl2br(e(strip_tags((string) $advert->description))) !!}</div>
                </div>

                <div class="mt-7 border border-[#e8e8e8] rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-[15px] text-[#888]">Seller</div>
                            <div class="text-[18px] text-[#111] font-semibold mt-1">{{ $advert->user->name ?? 'Seller' }}</div>
                            <div class="text-[15px] text-[#666] mt-2">{{ $advert->city ?: ($advert->user->city ?? '-') }}</div>
                            @if($advert->show_phone && !empty($advert->user?->phone))
                                <div class="text-[15px] text-[#666] mt-1">{{ $advert->user->phone }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showEnquiryModal" x-transition.opacity class="fixed inset-0 z-[80] bg-black/60 p-4 flex items-center justify-center" style="display:none;" @click.self="showEnquiryModal = false">
            <div class="w-full max-w-[640px] bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-[20px] font-semibold text-[#111]">Send Enquiry</h3>
                    <button type="button" class="text-[#888] text-[20px]" @click="showEnquiryModal = false">&times;</button>
                </div>

                <div class="mt-4 rounded-xl bg-[#f5f5f5] p-2 flex items-center gap-4">
                    <img src="{{ $galleryImages[0] ?? '' }}" alt="{{ $advert->title }}" class="w-16 h-16 rounded-lg object-cover border border-[#ddd]">
                    <div>
                        <div class="text-[18px] font-medium text-[#111] leading-tight">{{ $advert->title }}</div>
                        <div class="text-[14px] text-[#c8a95a] font-semibold">&pound;{{ number_format((float) $advert->price, 0) }}</div>
                    </div>
                </div>

                <textarea x-model="enquiryMessage" class="mt-4 w-full border border-[#cfcfcf] rounded-xl px-4 py-3 text-[14px]" rows="4" placeholder="Hi, I'm interested in this watch. Is it still available?"></textarea>

                <div class="mt-4 rounded-xl bg-[#fcf6ea] px-4 py-3 text-[13px] text-[#9a4f00]">
                    <strong>Safety Tip:</strong> Never send payment without viewing the watch in person.
                </div>

                <button type="button" @click="sendEnquiry()" class="mt-4 w-full h-[40px] rounded-xl bg-[#000] text-white text-[14px] font-semibold disabled:opacity-60" :disabled="enquiryMessage.trim().length === 0 || sending">
                    <span x-text="sending ? 'Sending...' : 'Send Message'"></span>
                </button>
            </div>
        </div>

        <div x-show="showLoginModal" x-transition.opacity class="fixed inset-0 z-[80] bg-black/60 p-4 flex items-center justify-center" style="display:none;" @click.self="showLoginModal = false">
            <div class="w-full max-w-[520px] bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-[22px] font-semibold text-[#111]">Login Required</h3>
                <p class="text-[16px] text-[#555] mt-3">Please login to enquire about this watch.</p>
                <div class="mt-5 flex items-center gap-3">
                    <a :href="loginUrl" class="inline-flex items-center justify-center h-[40px] px-5 rounded-xl bg-black text-white no-underline text-[14px] font-semibold">Login</a>
                    <button type="button" @click="showLoginModal = false" class="inline-flex items-center justify-center h-[40px] px-5 rounded-xl border border-[#ddd] text-[14px] text-[#444] bg-white">Cancel</button>
                </div>
            </div>
        </div>
    </section>

    <style>
        .watch-thumb-swiper .swiper-slide { width: auto; }
        .watch-thumb-swiper .watch-thumb-item { transition: border-color .2s ease, box-shadow .2s ease; }
        .watch-thumb-swiper .swiper-slide-thumb-active .watch-thumb-item { border-color: #d4b160; box-shadow: 0 0 0 1px #d4b160 inset; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        function watchShowPage(config) {
            return {
                isLoggedIn: !!config.isLoggedIn,
                loginUrl: config.loginUrl,
                enquireUrl: config.enquireUrl,
                messagesUrl: config.messagesUrl,
                showEnquiryModal: false,
                showLoginModal: false,
                enquiryMessage: "Hi, I'm interested in this watch. Is it still available?",
                sending: false,
                openEnquiry() {
                    if (this.isLoggedIn) {
                        this.showEnquiryModal = true;
                        return;
                    }
                    this.showLoginModal = true;
                },
                async sendEnquiry() {
                    if (this.enquiryMessage.trim().length === 0 || this.sending) {
                        return;
                    }

                    this.sending = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch(this.enquireUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                            },
                            body: JSON.stringify({ message: this.enquiryMessage.trim() }),
                        });
                        const payload = await response.json();

                        if (!response.ok || !payload.ok) {
                            throw new Error(payload.message || 'Unable to send enquiry.');
                        }

                        window.location.href = payload.redirect_url || this.messagesUrl;
                    } catch (error) {
                        alert(error.message || 'Unable to send enquiry.');
                    } finally {
                        this.sending = false;
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const thumbsEl = document.querySelector('.watch-thumb-swiper');
            const mainEl = document.querySelector('.watch-main-swiper');
            if (!mainEl || typeof Swiper === 'undefined') return;

            let thumbs = null;
            if (thumbsEl) {
                thumbs = new Swiper('.watch-thumb-swiper', {
                    spaceBetween: 8,
                    slidesPerView: 'auto',
                    freeMode: true,
                    watchSlidesProgress: true,
                });
            }

            new Swiper('.watch-main-swiper', {
                spaceBetween: 10,
                slidesPerView: 1,
                navigation: {
                    nextEl: '.watch-main-next',
                    prevEl: '.watch-main-prev',
                },
                thumbs: thumbs ? { swiper: thumbs } : undefined,
            });
        });
    </script>
</x-main-layout>
