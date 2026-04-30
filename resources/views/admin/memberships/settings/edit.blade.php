@extends('layouts.admin')
@section('title', 'Membership Billing Settings')

@section('content')
<div class="max-w-5xl space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Membership Billing Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Manage invoice branding and Stripe checkout credentials for trade and private seller package purchases.</p>
    </div>

    <form method="POST" action="{{ route('admin.membership-settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="bg-white rounded-lg shadow-sm p-6 space-y-5">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Invoice Details</h3>
                <p class="text-sm text-gray-500 mt-1">These details are printed on membership invoices.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                <input type="text" name="invoice_company_name" value="{{ old('invoice_company_name', $settings['invoice_company_name']) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                @error('invoice_company_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>



            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Registered Address</label>
                <textarea name="invoice_registered_address" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ old('invoice_registered_address', $settings['invoice_registered_address']) }}</textarea>
                @error('invoice_registered_address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Logo</label>
                <input type="file" name="invoice_logo" accept=".jpg,.jpeg,.png,.webp"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                @error('invoice_logo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                @if(!empty($settings['invoice_logo_path']))
                    <div class="mt-3 flex items-center gap-4">
                        <img src="{{ str_starts_with(ltrim($settings['invoice_logo_path'], '/'), 'images/') ? asset(ltrim($settings['invoice_logo_path'], '/')) : Storage::disk('public')->url($settings['invoice_logo_path']) }}" alt="Invoice logo" class="h-14 w-auto border border-gray-200 rounded p-1 bg-white">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300">
                            Remove logo
                        </label>
                    </div>
                @endif
            </div>
        </section>

        <section class="bg-white rounded-lg shadow-sm p-6 space-y-5">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Stripe Checkout</h3>
                <p class="text-sm text-gray-500 mt-1">Add both test and live credentials here, then switch modes when you are ready to go production.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Active Mode</label>
                    <select name="stripe_mode" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="test" {{ old('stripe_mode', $settings['stripe_mode']) === 'test' ? 'selected' : '' }}>Test</option>
                        <option value="live" {{ old('stripe_mode', $settings['stripe_mode']) === 'live' ? 'selected' : '' }}>Live</option>
                    </select>
                    @error('stripe_mode')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Webhook URL</label>
                    <input type="text" readonly value="{{ $webhookUrl }}"
                           class="w-full border border-gray-200 bg-gray-50 rounded px-3 py-2 text-sm text-gray-600">
                    <p class="text-xs text-gray-500 mt-2">Add this URL in your Stripe dashboard and use the matching webhook secret below.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Test Credentials</h4>
                        <p class="text-xs text-gray-500 mt-1">Used when active mode is set to Test.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publishable Key</label>
                        <input type="text" name="stripe_test_publishable_key" value="{{ old('stripe_test_publishable_key', $settings['stripe_test_publishable_key']) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        @error('stripe_test_publishable_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                        <input type="password" name="stripe_test_secret_key" value="{{ old('stripe_test_secret_key', $settings['stripe_test_secret_key']) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" autocomplete="off">
                        @error('stripe_test_secret_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret</label>
                        <input type="password" name="stripe_test_webhook_secret" value="{{ old('stripe_test_webhook_secret', $settings['stripe_test_webhook_secret']) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" autocomplete="off">
                        @error('stripe_test_webhook_secret')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Live Credentials</h4>
                        <p class="text-xs text-gray-500 mt-1">Used when active mode is set to Live.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publishable Key</label>
                        <input type="text" name="stripe_live_publishable_key" value="{{ old('stripe_live_publishable_key', $settings['stripe_live_publishable_key']) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        @error('stripe_live_publishable_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                        <input type="password" name="stripe_live_secret_key" value="{{ old('stripe_live_secret_key', $settings['stripe_live_secret_key']) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" autocomplete="off">
                        @error('stripe_live_secret_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret</label>
                        <input type="password" name="stripe_live_webhook_secret" value="{{ old('stripe_live_webhook_secret', $settings['stripe_live_webhook_secret']) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" autocomplete="off">
                        @error('stripe_live_webhook_secret')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        <div class="pt-2">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
