@php
    $isEdit = isset($level);
@endphp

<section class="bg-white rounded-lg shadow-sm p-6 space-y-6"
    x-data="{ sellerType: '{{ old('seller_type', $level->seller_type ?? 'both') }}' }">
    <h2 class="text-lg font-bold text-gray-800">General Information</h2>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Name *</label>
        <input type="text" name="name" value="{{ old('name', $level->name ?? '') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Package For *</label>
        <select name="seller_type" x-model="sellerType" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="both" {{ old('seller_type', $level->seller_type ?? 'both') === 'both' ? 'selected' : '' }}>Both</option>
            <option value="private_seller" {{ old('seller_type', $level->seller_type ?? 'both') === 'private_seller' ? 'selected' : '' }}>Private Seller</option>
            <option value="trade_seller" {{ old('seller_type', $level->seller_type ?? 'both') === 'trade_seller' ? 'selected' : '' }}>Trade Seller</option>
        </select>
        @error('seller_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div x-show="sellerType === 'private_seller'" x-cloak>
        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Private Seller Advert Price Range *</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Minimum Advert Price (£)</label>
                <input type="number" name="private_min_advert_price" step="0.01" min="0"
                    value="{{ old('private_min_advert_price', $level->private_min_advert_price ?? '') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                @error('private_min_advert_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Maximum Advert Price (£)</label>
                <input type="number" name="private_max_advert_price" step="0.01" min="0"
                    value="{{ old('private_max_advert_price', $level->private_max_advert_price ?? '') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                @error('private_max_advert_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Only applicable when package type is Private Seller.</p>
    </div>

    <div x-show="sellerType === 'trade_seller'" x-cloak>
        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Trade Seller Maximum Advert Count *</label>
        <input type="number" name="trade_max_advert_count" min="-1"
            value="{{ old('trade_max_advert_count', $level->trade_max_advert_count ?? '') }}"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
        @error('trade_max_advert_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        <p class="text-xs text-gray-500 mt-2">Only applicable when package type is Trade Seller. Use <strong>-1</strong> for unlimited ads.</p>
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Description</label>
        <textarea name="description" rows="4"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('description', $level->description ?? '') }}</textarea>
        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Confirmation Message</label>
        <textarea name="confirmation_message" rows="3"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('confirmation_message', $level->confirmation_message ?? '') }}</textarea>
        @error('confirmation_message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Initial Payment (£)</label>
            <input type="number" name="initial_payment" step="0.01" min="0"
                value="{{ old('initial_payment', $level->initial_payment ?? 0) }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            @error('initial_payment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-end pb-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="allow_signups" value="1" class="rounded border-gray-300"
                    {{ old('allow_signups', $level->allow_signups ?? true) ? 'checked' : '' }}>
                Allow Signups
            </label>
        </div>
        <div class="flex items-end pb-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
                    {{ old('is_active', $level->is_active ?? true) ? 'checked' : '' }}>
                Active
            </label>
        </div>
    </div>
</section>

<section class="bg-white rounded-lg shadow-sm p-6 space-y-6">
    <h2 class="text-lg font-bold text-gray-800">Billing Details</h2>

    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="has_recurring" value="1" class="rounded border-gray-300"
            {{ old('has_recurring', $level->has_recurring ?? false) ? 'checked' : '' }}>
        Has Recurring Subscription
    </label>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Billing Amount (£)</label>
            <input type="number" name="billing_amount" step="0.01" min="0"
                value="{{ old('billing_amount', $level->billing_amount ?? '') }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            @error('billing_amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Every</label>
            <input type="number" name="billing_every" min="1"
                value="{{ old('billing_every', $level->billing_every ?? '') }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            @error('billing_every')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Period</label>
            <select name="billing_period" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">-- Select --</option>
                @foreach(['day', 'week', 'month', 'year'] as $period)
                <option value="{{ $period }}" {{ old('billing_period', $level->billing_period ?? '') === $period ? 'selected' : '' }}>
                    {{ ucfirst($period) }}
                </option>
                @endforeach
            </select>
            @error('billing_period')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Billing Cycle Limit</label>
            <input type="number" name="billing_cycle_limit" min="0"
                value="{{ old('billing_cycle_limit', $level->billing_cycle_limit ?? 0) }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            @error('billing_cycle_limit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="has_trial" value="1" class="rounded border-gray-300"
            {{ old('has_trial', $level->has_trial ?? false) ? 'checked' : '' }}>
        Has Custom Trial
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Trial Amount (£)</label>
            <input type="number" name="trial_amount" step="0.01" min="0"
                value="{{ old('trial_amount', $level->trial_amount ?? 0) }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            @error('trial_amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Trial Cycles</label>
            <input type="number" name="trial_cycles" min="0"
                value="{{ old('trial_cycles', $level->trial_cycles ?? 0) }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            @error('trial_cycles')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</section>

<section class="bg-white rounded-lg shadow-sm p-6 space-y-6">
    <h2 class="text-lg font-bold text-gray-800">Expiration Settings</h2>

    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="has_expiration" value="1" class="rounded border-gray-300"
            {{ old('has_expiration', $level->has_expiration ?? false) ? 'checked' : '' }}>
        Membership Expiration Enabled
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Expiration Number</label>
            <input type="number" name="expiration_number" min="1"
                value="{{ old('expiration_number', $level->expiration_number ?? '') }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            @error('expiration_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Expiration Unit</label>
            <select name="expiration_unit" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">-- Select --</option>
                @foreach(['day', 'week', 'month', 'year'] as $unit)
                <option value="{{ $unit }}" {{ old('expiration_unit', $level->expiration_unit ?? '') === $unit ? 'selected' : '' }}>
                    {{ ucfirst($unit) }}
                </option>
                @endforeach
            </select>
            @error('expiration_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</section>

<div class="pt-2 flex gap-3">
    <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
        {{ $isEdit ? 'Update Level' : 'Create Level' }}
    </button>
    <a href="{{ route('admin.membership-levels.index') }}" class="px-5 py-2 rounded text-sm text-gray-600 border hover:bg-gray-50">Cancel</a>
</div>
