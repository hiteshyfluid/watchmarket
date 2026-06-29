<x-main-layout>
    <section class="bg-gray-100 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold text-gray-900">Package Upgraded!</h1>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-flash-messages class="mb-8" />

            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900">Your package has been successfully upgraded.</h2>
                <p class="mt-6 text-xl text-gray-600">Your advert's new expiry date is now active.</p>
                <div class="flex items-center justify-center gap-6 mt-8">
                    @if($order->advert)
                        <a href="{{ route('market.show', $order->advert) }}" class="inline-block text-black font-semibold border-b border-black no-underline">
                            View Advert
                        </a>
                    @endif
                    <a href="{{ route('my-account') }}" class="inline-block text-black font-semibold border-b border-black no-underline">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="bg-gray-50 border rounded p-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">Your Invoice Details</h3>

                <ul class="list-disc pl-6 text-gray-700 space-y-1 mb-8">
                    <li>Account: {{ $order->user?->name }} ({{ $order->user?->email }})</li>
                    <li>Advert: {{ $order->advert?->title ?? '-' }}</li>
                    <li>Package: {{ $order->level?->name ?? '-' }}</li>
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
        </div>
    </section>
</x-main-layout>
