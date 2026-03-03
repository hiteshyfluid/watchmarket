<footer class="bg-[#050505] text-[#b8b8b8] mt-auto">
    <div class="site-container px-5 lg:px-8 pt-16 pb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <div>
                <a href="/" class="flex items-center gap-3 text-white no-underline mb-5">
                    <img src="{{ asset('images/whitelogo.webp') }}" alt="WatchMarket" class="w-full h-10 object-contain">
                </a>
                <p class="text-[14px] leading-7 max-w-[320px] text-[#b8b8b8]">
                    The trusted marketplace for luxury timepieces. Connect with verified sellers and find your next watch.
                </p>
            </div>

            <div>
                <h4 class="text-white text-[18px] font-semibold mb-5">Browse</h4>
                <ul class="space-y-3 text-[14px]">
                    <li><a href="{{ route('market.index') }}" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">All Watches</a></li>
                    <li><a href="{{ route('market.index', ['q' => 'Rolex']) }}" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Rolex</a></li>
                    <li><a href="{{ route('market.index', ['q' => 'Omega']) }}" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Omega</a></li>
                    <li><a href="{{ route('market.index', ['q' => 'Patek Philippe']) }}" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Patek Philippe</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white text-[18px] font-semibold mb-5">Sell</h4>
                <ul class="space-y-3 text-[14px]">
                    <li><a href="{{ route('sell-watch') }}" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Create Listing</a></li>
                    <li><a href="{{ route('seller.trade.packages') }}" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Pricing</a></li>
                    <li><a href="{{ route('seller.trade.packages') }}" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Dealer Plans</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white text-[18px] font-semibold mb-5">Support</h4>
                <ul class="space-y-3 text-[14px]">
                    <li><a href="#" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Help Centre</a></li>
                    <li><a href="#" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Safety Tips</a></li>
                    <li><a href="#" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Contact Us</a></li>
                    <li><a href="#" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Report a Listing</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4 text-[14px]">
            <div class="text-[#b8b8b8]">&copy; {{ now()->year }} WatchMarket. All rights reserved.</div>
            <div class="flex items-center gap-8">
                <a href="#" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Privacy Policy</a>
                <a href="#" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Terms of Service</a>
                <a href="#" class="no-underline text-[#b8b8b8] hover:text-white text-[14px]">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
