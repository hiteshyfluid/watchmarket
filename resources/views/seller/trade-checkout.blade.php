<x-main-layout>
    <section class="bg-gray-100 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        </div>
    </section>

    <section class="py-12 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded mb-6 text-sm">
                <strong>Please fix the errors below:</strong>
                <ul class="list-disc list-inside mt-1 space-y-0.5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('seller.trade.checkout.process', $level) }}" class="space-y-6">
                @csrf

                <div class="bg-white border rounded p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Subscription Level</h2>
                            <p class="text-gray-600 mt-1">{{ $level->name }}</p>
                        </div>
                        <a href="{{ route('seller.trade.packages') }}" class="text-sm text-blue-600 hover:underline">Change Subscription</a>
                    </div>
                    <div class="mt-4 text-sm text-gray-700">
                        <p>{{ $level->billingSummary() }}</p>
                        @if($level->tradeAdvertLimitLabel())
                        <p class="mt-1">{{ $level->tradeAdvertLimitLabel() }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white border rounded p-6 space-y-4">
                    <h2 class="text-2xl font-bold text-gray-900">Billing Address</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">First Name</label>
                            <input type="text" name="first_name" required value="{{ old('first_name', $user->first_name) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">Last Name</label>
                            <input type="text" name="last_name" required value="{{ old('last_name', $user->last_name) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">Address</label>
                        <input type="text" name="address" required value="{{ old('address', $user->address) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">City</label>
                            <input type="text" name="city" required value="{{ old('city', $user->city) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">Postal Code</label>
                            <input type="text" name="postal_code" required value="{{ old('postal_code', $user->postal_code) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">Country</label>
                            <input type="text" name="country" required value="{{ old('country', $user->country) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">Phone</label>
                            <input type="text" name="phone" required value="{{ old('phone', $user->phone) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wider">Email Address</label>
                            <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded p-6 space-y-4">
                    <h2 class="text-2xl font-bold text-gray-900">Payment Information</h2>
                    @if($requiresStripe)
                    <p class="text-sm text-gray-500">You will be redirected to Stripe {{ strtolower($stripeMode) }} checkout to pay securely by card. Your subscription will activate only after Stripe confirms the payment.</p>
                    @else
                    <p class="text-sm text-gray-500">This package has no charge today. Submitting this form will activate the subscription immediately.</p>
                    @endif
                </div>

                <div>
                    <button type="submit" class="bg-black text-white font-bold py-3 px-8 uppercase tracking-widest hover:bg-gray-800 transition">
                        {{ $requiresStripe ? 'Continue Payment' : 'Activate Subscription' }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-main-layout>
