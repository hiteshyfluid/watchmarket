@extends('layouts.admin')
@section('title', 'Membership Billing Settings')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Membership Billing Settings</h2>
        <p class="text-sm text-gray-500 mt-1">These details are printed on membership invoices (trade and private seller orders).</p>
    </div>

    <form method="POST" action="{{ route('admin.membership-settings.update') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
            <input type="text" name="invoice_company_name" value="{{ old('invoice_company_name', $settings['invoice_company_name']) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            @error('invoice_company_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">VAT Number</label>
                <input type="text" name="invoice_vat_number" value="{{ old('invoice_vat_number', $settings['invoice_vat_number']) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                @error('invoice_vat_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="invoice_vat_rate" value="{{ old('invoice_vat_rate', $settings['invoice_vat_rate']) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                @error('invoice_vat_rate')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
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
                    <img src="{{ Storage::disk('public')->url($settings['invoice_logo_path']) }}" alt="Invoice logo" class="h-14 w-auto border border-gray-200 rounded p-1 bg-white">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300">
                        Remove logo
                    </label>
                </div>
            @endif
            <p class="text-xs text-gray-500 mt-2">Logo is managed from admin and used for invoice branding.</p>
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

