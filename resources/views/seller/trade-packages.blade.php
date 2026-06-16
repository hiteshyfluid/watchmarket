<x-main-layout>
    <div class="min-h-screen bg-[#f7f7f7] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-flash-messages class="mb-8" />

            @if(auth()->check() && auth()->user()->isPrivateSeller())
            <div class="mb-8 rounded-2xl border border-[#cfd8e3] bg-[#eef3f8] px-6 py-5">
                <h3 class="text-[17px] font-semibold text-[#1f3a57]">Upgrading to a Trade Seller</h3>
                <p class="mt-2 text-[14px] leading-6 text-[#35516f]">
                    Any existing adverts created as a <strong>Private Seller</strong> will be upgraded to <strong>Trade Listings</strong>, giving them all the benefits of a Watch Market Trade Seller account, including increased visibility and enhanced features.
                </p>
                <p class="mt-2 text-[14px] leading-6 text-[#35516f]">
                    Your existing private adverts will remain active, be converted into Trade Listings, and will count towards (and be deducted from) your available trade package allowance.
                </p>
            </div>
            @endif

            @if($levels->where('has_trial', true)->where('trial_amount', '0.00')->isNotEmpty())
            <div class="mb-8 bg-[#d4b160] text-[#111] text-center py-4 px-6 rounded-lg font-semibold text-[17px] tracking-wide">
                Select Trade Subscription – Get 3 Months Free
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($levels as $level)
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
                        <a href="{{ route('seller.trade.checkout', $level) }}"
                           class="inline-flex items-center justify-center w-full bg-black text-white font-bold py-3 text-[13px] uppercase tracking-widest hover:bg-[#222] transition no-underline">
                            Select Package
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center text-[#888] text-[16px]">
                    No trade packages are available right now.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-main-layout>
