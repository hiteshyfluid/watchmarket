<x-main-layout>
    <div x-data="{ open: 1 }" class="min-h-screen bg-white">

        {{-- Hero --}}
        <section class="bg-[#f2f2f2] py-20 px-4 text-center">
            <p class="text-[11px] font-semibold tracking-[0.3em] uppercase text-[#999] mb-5">Safety Guide</p>
            <h1 class="text-[52px] sm:text-[64px] font-black text-[#111] leading-none mb-5">
                Staying Safe When<br>Buying or Selling a Watch
            </h1>
            <p class="text-[16px] text-[#666] max-w-[520px] mx-auto leading-relaxed">
                WatchMarket connects buyers and sellers directly. That means you're in control — but it also means taking a few sensible precautions before agreeing to a deal. Whether you're spending £500 or £50,000, the same principles apply.
            </p>
        </section>

        <div class="border-t border-[#e8e8e8]"></div>

        {{-- Accordion --}}
        <div class="max-w-2xl mx-auto px-6 py-10 divide-y divide-[#e8e8e8]">

            {{-- 1. Where to Meet --}}
            <div>
                <button @click="open = open === 1 ? null : 1"
                        class="w-full flex items-center gap-4 py-5 text-left group">
                    <span class="w-8 h-8 rounded flex items-center justify-center text-[12px] font-bold shrink-0 transition-colors duration-200"
                          :class="open === 1 ? 'bg-[#d4b160] text-[#111]' : 'bg-[#f0f0f0] text-[#888]'">1</span>
                    <span class="flex-1 text-[15px] font-semibold text-[#111]">Where to Meet</span>
                    <svg :class="open === 1 ? 'rotate-180' : ''" class="w-4 h-4 text-[#aaa] shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="grid transition-all duration-300 ease-in-out"
                     :style="open === 1 ? 'grid-template-rows: 1fr' : 'grid-template-rows: 0fr'">
                    <div class="overflow-hidden">
                        <div class="pb-8">
                            <p class="text-[14px] text-[#555] leading-[1.75] mb-6">
                                Never arrange a transaction somewhere you feel uncomfortable. High-value watches attract opportunists, so where you meet matters.
                            </p>
                            <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">Best Options</p>
                            <ul class="space-y-3 mb-7">
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">A jeweller or watch dealer's premises</strong> — this is our top recommendation. A reputable jeweller can inspect the watch on the spot and confirm authenticity. Many are happy to facilitate private transactions for a small fee.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">The seller's registered business address</strong> — for trade sellers, meeting at their shop or premises is perfectly normal and gives you confidence they're legitimate.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">A bank branch</strong> — many banks will allow cash counting or verification on the premises. Some buyers prefer this for high-value cash transactions.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">A busy public place</strong> — a town centre coffee shop or hotel lobby is a reasonable option for lower-value watches. Avoid car parks, private residences, or isolated locations.</span>
                                </li>
                            </ul>
                            <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">Want to Avoid</p>
                            <ul class="space-y-2">
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Meeting at a private address you've not verified</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Late evening or out-of-hours meetings in quiet locations</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Taking large amounts of cash to an unfamiliar area</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Going alone for a high-value transaction — bring someone with you if you can</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Checking the Watch is Genuine --}}
            <div>
                <button @click="open = open === 2 ? null : 2"
                        class="w-full flex items-center gap-4 py-5 text-left">
                    <span class="w-8 h-8 rounded flex items-center justify-center text-[12px] font-bold shrink-0 transition-colors duration-200"
                          :class="open === 2 ? 'bg-[#d4b160] text-[#111]' : 'bg-[#f0f0f0] text-[#888]'">2</span>
                    <span class="flex-1 text-[15px] font-semibold text-[#111]">Checking the Watch is Genuine</span>
                    <svg :class="open === 2 ? 'rotate-180' : ''" class="w-4 h-4 text-[#aaa] shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="grid transition-all duration-300 ease-in-out"
                     :style="open === 2 ? 'grid-template-rows: 1fr' : 'grid-template-rows: 0fr'">
                    <div class="overflow-hidden">
                        <div class="pb-8">
                            <p class="text-[14px] text-[#555] leading-[1.75] mb-6">
                                The pre-owned market is not immune to fakes, stolen goods, and misrepresentation. Do your checks before any money changes hands.
                            </p>
                            <div class="bg-[#f7f7f7] border border-[#e5e5e5] rounded p-5 mb-6">
                                <p class="text-[13px] font-bold text-[#111] mb-2">Check the serial number against the Watch Register</p>
                                <p class="text-[13px] text-[#555] leading-[1.7] mb-2">
                                    Before completing any purchase, check the watch's serial number against the Watch Register — the Global Database of Lost, Stolen and Counterfeit Watches. It's free to search and takes under a minute.
                                </p>
                                <p class="text-[12px] text-[#d4b160] mb-3">thewatchregister.com</p>
                                <p class="text-[13px] text-[#555] leading-[1.7]">
                                    The serial number is typically found on the case-back or between the lugs. You may need a loupe or magnifying glass to read it clearly.
                                </p>
                            </div>
                            <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">Things to Check in Person</p>
                            <ul class="space-y-3">
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Box and papers.</strong> Original documentation significantly increases confidence and value. Check that the serial number on the papers matches the watch.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Physical inspection.</strong> Look for consistent finishing, correct font on the dial, smooth crown operation, and proper weight. Counterfeits often feel lighter and have printing inconsistencies.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Independent authentication.</strong> For watches over £2,000, consider paying for a professional appraisal. Most reputable jewellers offer this service.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Service history.</strong> Ask whether the watch has been serviced and by whom. Factory service records add confidence.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Seller's history.</strong> Has the seller listed before? Do they have other listings consistent with being a genuine collector or dealer? Be more cautious with first-time listers selling high-value items.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Safe Payment Practices --}}
            <div>
                <button @click="open = open === 3 ? null : 3"
                        class="w-full flex items-center gap-4 py-5 text-left">
                    <span class="w-8 h-8 rounded flex items-center justify-center text-[12px] font-bold shrink-0 transition-colors duration-200"
                          :class="open === 3 ? 'bg-[#d4b160] text-[#111]' : 'bg-[#f0f0f0] text-[#888]'">3</span>
                    <span class="flex-1 text-[15px] font-semibold text-[#111]">Safe Payment Practices</span>
                    <svg :class="open === 3 ? 'rotate-180' : ''" class="w-4 h-4 text-[#aaa] shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="grid transition-all duration-300 ease-in-out"
                     :style="open === 3 ? 'grid-template-rows: 1fr' : 'grid-template-rows: 0fr'">
                    <div class="overflow-hidden">
                        <div class="pb-8">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="bg-[#f7f7f7] border border-[#e5e5e5] rounded p-5">
                                    <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-4">If You're Selling</p>
                                    <ul class="space-y-4">
                                        <li class="flex gap-3 text-[13px] text-[#444] leading-[1.7]">
                                            <span class="mt-[8px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                            <span><strong class="text-[#111] font-semibold">Wait for cleared funds.</strong> Do not hand over the watch until the money is confirmed as cleared in your bank — not just pending or shown as a screenshot.</span>
                                        </li>
                                        <li class="flex gap-3 text-[13px] text-[#444] leading-[1.7]">
                                            <span class="mt-[8px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                            <span><strong class="text-[#111] font-semibold">Beware overpayment scams.</strong> A buyer sending more than agreed and asking for a refund is a classic fraud. The original payment will bounce; your refund won't.</span>
                                        </li>
                                        <li class="flex gap-3 text-[13px] text-[#444] leading-[1.7]">
                                            <span class="mt-[8px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                            <span><strong class="text-[#111] font-semibold">Avoid third-party payment requests.</strong> If someone else is paying on the buyer's behalf, treat it as a red flag.</span>
                                        </li>
                                        <li class="flex gap-3 text-[13px] text-[#444] leading-[1.7]">
                                            <span class="mt-[8px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                            <span><strong class="text-[#111] font-semibold">Don't ship before payment clears.</strong> Once a watch is posted, recovering it is very difficult.</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="bg-[#f7f7f7] border border-[#e5e5e5] rounded p-5">
                                    <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-4">If You're Buying</p>
                                    <ul class="space-y-4">
                                        <li class="flex gap-3 text-[13px] text-[#444] leading-[1.7]">
                                            <span class="mt-[8px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                            <span><strong class="text-[#111] font-semibold">Use bank transfer where possible.</strong> It's traceable. Avoid cash for high-value watches unless meeting at a verified premises.</span>
                                        </li>
                                        <li class="flex gap-3 text-[13px] text-[#444] leading-[1.7]">
                                            <span class="mt-[8px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                            <span><strong class="text-[#111] font-semibold">Never pay by gift card, crypto, or international wire</strong> to a party you haven't verified. These methods offer no recourse.</span>
                                        </li>
                                        <li class="flex gap-3 text-[13px] text-[#444] leading-[1.7]">
                                            <span class="mt-[8px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                            <span><strong class="text-[#111] font-semibold">Confirm bank details by phone.</strong> Before transferring any significant sum, call the seller on an independently verified number to confirm account details.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Spotting a Scam Listing --}}
            <div>
                <button @click="open = open === 4 ? null : 4"
                        class="w-full flex items-center gap-4 py-5 text-left">
                    <span class="w-8 h-8 rounded flex items-center justify-center text-[12px] font-bold shrink-0 transition-colors duration-200"
                          :class="open === 4 ? 'bg-[#d4b160] text-[#111]' : 'bg-[#f0f0f0] text-[#888]'">4</span>
                    <span class="flex-1 text-[15px] font-semibold text-[#111]">Spotting a Scam Listing</span>
                    <svg :class="open === 4 ? 'rotate-180' : ''" class="w-4 h-4 text-[#aaa] shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="grid transition-all duration-300 ease-in-out"
                     :style="open === 4 ? 'grid-template-rows: 1fr' : 'grid-template-rows: 0fr'">
                    <div class="overflow-hidden">
                        <div class="pb-8">
                            <p class="text-[14px] text-[#555] leading-[1.75] mb-5">
                                Most listings on WatchMarket are genuine, but if something feels off, it probably is. Watch for:
                            </p>
                            <ul class="space-y-3">
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>A price significantly below market value — check recent sold prices on Chrono24 or WatchFinder to sense-check</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Stock photos instead of actual photos of the watch</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Vague or evasive answers when you ask basic questions</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Pressure to complete quickly or move the conversation off-platform</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>A seller unwilling to meet in person or let the watch be inspected</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span>Inconsistencies between the listing description and photos</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Report a Suspicious Listing --}}
            <div>
                <button @click="open = open === 5 ? null : 5"
                        class="w-full flex items-center gap-4 py-5 text-left">
                    <span class="w-8 h-8 rounded flex items-center justify-center text-[12px] font-bold shrink-0 transition-colors duration-200"
                          :class="open === 5 ? 'bg-[#d4b160] text-[#111]' : 'bg-[#f0f0f0] text-[#888]'">5</span>
                    <span class="flex-1 text-[15px] font-semibold text-[#111]">Report a Suspicious Listing</span>
                    <svg :class="open === 5 ? 'rotate-180' : ''" class="w-4 h-4 text-[#aaa] shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="grid transition-all duration-300 ease-in-out"
                     :style="open === 5 ? 'grid-template-rows: 1fr' : 'grid-template-rows: 0fr'">
                    <div class="overflow-hidden">
                        <div class="pb-8">
                            <p class="text-[14px] text-[#555] leading-[1.75] mb-5">
                                If you come across a listing you believe to be fraudulent, counterfeit, or advertising a stolen watch, please report it. You could save another buyer from a serious loss.
                            </p>
                            <ul class="space-y-4">
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">On the listing:</strong> use the Report button to flag it directly to our team.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Via our Report a Listing page:</strong> Include the listing URL, the issue, and any evidence you have. <a href="{{ route('contact.show') }}" class="text-[#d4b160] hover:underline no-underline">Report a Listing →</a></span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Check the Watch Register:</strong> If you believe a watch is stolen, report it directly at <span class="text-[#d4b160]">thewatchregister.com</span></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. If Something Goes Wrong --}}
            <div>
                <button @click="open = open === 6 ? null : 6"
                        class="w-full flex items-center gap-4 py-5 text-left">
                    <span class="w-8 h-8 rounded flex items-center justify-center text-[12px] font-bold shrink-0 transition-colors duration-200"
                          :class="open === 6 ? 'bg-[#d4b160] text-[#111]' : 'bg-[#f0f0f0] text-[#888]'">6</span>
                    <span class="flex-1 text-[15px] font-semibold text-[#111]">If Something Goes Wrong</span>
                    <svg :class="open === 6 ? 'rotate-180' : ''" class="w-4 h-4 text-[#aaa] shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="grid transition-all duration-300 ease-in-out"
                     :style="open === 6 ? 'grid-template-rows: 1fr' : 'grid-template-rows: 0fr'">
                    <div class="overflow-hidden">
                        <div class="pb-8">
                            <p class="text-[14px] text-[#555] leading-[1.75] mb-5">
                                If you believe you've been defrauded:
                            </p>
                            <ul class="space-y-4">
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Report to Action Fraud</strong> — the UK's national fraud reporting centre: <span class="text-[#d4b160]">actionfraud.police.uk</span></span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Contact your bank immediately</strong> — if a fraudulent transfer has been made, they may be able to recall it under the Faster Payments recall scheme.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Notify WatchMarket</strong> via our <a href="{{ route('contact.show') }}" class="text-[#d4b160] hover:underline no-underline">Contact Us</a> page so we can remove the listing and prevent further harm to others.</span>
                                </li>
                                <li class="flex gap-3 text-[14px] text-[#444] leading-[1.7]">
                                    <span class="mt-[9px] w-1.5 h-1.5 rounded-full bg-[#888] shrink-0"></span>
                                    <span><strong class="text-[#111] font-semibold">Report the watch as stolen</strong> to the Watch Register if applicable.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Still have questions? --}}
        <div class="border-t border-[#e8e8e8]">
            <div class="max-w-2xl mx-auto px-6 py-12">
                <h2 class="text-[22px] font-bold text-[#111] mb-2">Still have questions?</h2>
                <p class="text-[14px] text-[#555] leading-relaxed mb-5">
                    Our team is happy to help. Visit our Contact Us page and we'll get back to you as soon as possible.
                </p>
                <a href="{{ route('contact.show') }}"
                   class="text-[11px] font-bold tracking-[0.2em] uppercase text-[#d4b160] hover:underline no-underline">
                    Contact Us →
                </a>
            </div>
        </div>

    </div>
</x-main-layout>
