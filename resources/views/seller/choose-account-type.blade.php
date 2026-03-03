<x-main-layout>
    <div class="bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-gray-800">{{ __('Choose Account Type') }}</h1>
            <nav class="text-sm text-gray-500 mt-2">
                <a href="/" class="hover:underline">Home</a> » Choose Account Type
            </nav>
        </div>
    </div>

    <div class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Private Seller -->
                <div class="border rounded-sm overflow-hidden shadow-sm hover:shadow-md transition">
                    <div class="h-64 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Private Seller" class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <h2 class="text-2xl font-bold mb-4">Private Seller</h2>
                        <p class="text-gray-600 mb-8 text-sm">
                            It is easy to sell a single watch as a Private Seller with no fuss. Simply advertise your watch and wait for the calls to come in.
                        </p>
                        <ul class="space-y-4 mb-8 text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Choose this option to unlock the selling feature.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Create your listing for a competitive rate with no extra fees and commission free.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Upload up to 10 pictures.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Amend your advert at anytime during the time of your listing.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simple, easy and no fuss.
                            </li>
                        </ul>
                        <form action="{{ route('seller.update-account-type') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="private_seller">
                            <button type="submit" class="w-full bg-black text-white font-bold py-3 uppercase tracking-widest hover:bg-gray-800 transition">
                                Create Advert Now
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Trade Seller -->
                <div class="border rounded-sm overflow-hidden shadow-sm hover:shadow-md transition">
                    <div class="h-64 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Trade Seller" class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <h2 class="text-2xl font-bold mb-4">Trade Seller</h2>
                        <p class="text-gray-600 mb-8 text-sm">
                            Ideal for trade and watch dealers that want to sell single or multiple watches.
                        </p>
                        <ul class="space-y-4 mb-8 text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Register for a trade account to unlock competitive rates.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Sell single or multiple watches from little as 50p per listing.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                No additional fees or commission.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Upload up to 20 pictures.
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Unlock additional features to quickly sell your watch.
                            </li>
                        </ul>
                        <a href="{{ route('seller.trade.packages') }}"
                           class="w-full inline-flex items-center justify-center bg-black text-white font-bold py-3 uppercase tracking-widest hover:bg-gray-800 transition no-underline">
                            Continue
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
