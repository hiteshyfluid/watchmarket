<x-main-layout>
    <section class="bg-gray-100 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold text-gray-900">Thank You</h1>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-bold text-gray-900">Your Subscription is now active!</h2>
                <p class="mt-6 text-xl text-gray-600">Thank you for your purchase.</p>
                <a href="{{ route('adverts.create') }}" class="inline-block mt-8 text-black font-semibold border-b border-black no-underline">
                    Create an advert now?
                </a>
            </div>

            <div class="bg-gray-50 border rounded p-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">Your Invoice Details</h3>

                <ul class="list-disc pl-6 text-gray-700 space-y-1 mb-8">
                    <li>Account: {{ $order->user?->name }} ({{ $order->user?->email }})</li>
                    <li>Subscription: {{ $order->level?->name ?? '-' }}</li>
                    <li>Order Code: {{ $order->code }}</li>
                </ul>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t pt-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Billing Address</h4>
                        <p class="text-gray-600 whitespace-pre-line">{{ $order->billing_details ?: '-' }}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Payment Method</h4>
                        <p class="text-gray-600">{{ $order->gateway ?: 'Manual Checkout' }}</p>
                        <p class="text-gray-600">Transaction: {{ $order->payment_transaction_id ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Total Billed</h4>
                        <p class="text-2xl font-bold text-gray-900">£{{ number_format((float) $order->total, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('my-account') }}" class="text-blue-600 hover:underline">View Your Account &rarr;</a>
            </div>
        </div>
    </section>
</x-main-layout>

