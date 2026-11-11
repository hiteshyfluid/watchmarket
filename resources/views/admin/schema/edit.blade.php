@extends('layouts.admin')
@section('title', 'Schema Markup')

@php
    $val = fn (string $key) => old($key, $settings[$key] ?? '');
    $checked = fn (string $key) => old('_submitted') ? (bool) old($key) : (bool) ($settings[$key] ?? false);
    $input = 'w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-800';
    $label = 'block text-sm font-medium text-gray-700 mb-1';
    $hint = 'text-xs text-gray-500 mt-1';
    $newConditions = old('_submitted') ? (array) old('product_new_conditions', []) : (array) $settings['product_new_conditions'];
    $sameAs = old('organization_same_as', implode("\n", (array) $settings['organization_same_as']));
@endphp

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Schema Markup</h2>
        <p class="text-sm text-gray-500 mt-1">
            Structured data (JSON-LD) helps Google show your business details, prices and breadcrumbs in search results.
            Changes go live as soon as you save. Check a page with
            <a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener" class="underline text-gray-800">Google's Rich Results Test</a> afterwards.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded px-4 py-3 text-sm">
            <strong>Please fix the following:</strong>
            <ul class="list-disc list-inside mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">
        <form method="POST" action="{{ route('admin.schema.update') }}" class="xl:col-span-3 space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="_submitted" value="1">

            {{-- Organization --}}
            <section class="bg-white rounded-lg shadow-sm p-6 space-y-4" x-data="{ on: {{ $checked('organization_enabled') ? 'true' : 'false' }} }">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Organisation</h3>
                        <p class="{{ $hint }}">Your business details. Output on every page.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer shrink-0">
                        <input type="checkbox" name="organization_enabled" value="1" x-model="on" class="rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                        Enabled
                    </label>
                </div>

                <div x-show="on" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Name *</label>
                            <input type="text" name="organization_name" value="{{ $val('organization_name') }}" class="{{ $input }}" required>
                        </div>
                        <div>
                            <label class="{{ $label }}">Type</label>
                            <select name="organization_type" class="{{ $input }}">
                                @foreach(\App\Support\SchemaSettings::ORGANIZATION_TYPES as $type)
                                    <option value="{{ $type }}" @selected($val('organization_type') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <p class="{{ $hint }}">"Organization" suits most marketplaces. Use "LocalBusiness" only if customers visit a physical premises.</p>
                        </div>
                        <div>
                            <label class="{{ $label }}">Legal name</label>
                            <input type="text" name="organization_legal_name" value="{{ $val('organization_legal_name') }}" class="{{ $input }}" placeholder="e.g. WatchMarket Ltd">
                        </div>
                        <div>
                            <label class="{{ $label }}">VAT number</label>
                            <input type="text" name="organization_vat_id" value="{{ $val('organization_vat_id') }}" class="{{ $input }}" placeholder="e.g. GB123456789">
                        </div>
                    </div>

                    <div>
                        <label class="{{ $label }}">Description</label>
                        <textarea name="organization_description" rows="3" class="{{ $input }}" placeholder="One or two sentences about WatchMarket.">{{ $val('organization_description') }}</textarea>
                    </div>

                    <div>
                        <label class="{{ $label }}">Logo URL</label>
                        <input type="text" name="organization_logo" value="{{ $val('organization_logo') }}" class="{{ $input }}" placeholder="{{ asset('images/logo.webp') }}">
                        <p class="{{ $hint }}">Leave blank to use the site logo. Google prefers a square PNG or JPG at least 112×112px.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Public email</label>
                            <input type="email" name="organization_email" value="{{ $val('organization_email') }}" class="{{ $input }}" placeholder="e.g. support@watchmarket.co.uk">
                        </div>
                        <div>
                            <label class="{{ $label }}">Public phone</label>
                            <input type="text" name="organization_phone" value="{{ $val('organization_phone') }}" class="{{ $input }}" placeholder="e.g. +44 20 1234 5678">
                        </div>
                    </div>

                    <div>
                        <p class="{{ $label }}">Address <span class="font-normal text-gray-500">(optional)</span></p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="organization_street" value="{{ $val('organization_street') }}" class="{{ $input }} md:col-span-2" placeholder="Street address">
                            <input type="text" name="organization_locality" value="{{ $val('organization_locality') }}" class="{{ $input }}" placeholder="Town / city">
                            <input type="text" name="organization_region" value="{{ $val('organization_region') }}" class="{{ $input }}" placeholder="County / region">
                            <input type="text" name="organization_postcode" value="{{ $val('organization_postcode') }}" class="{{ $input }}" placeholder="Postcode">
                            <input type="text" name="organization_country" value="{{ $val('organization_country') }}" maxlength="2" class="{{ $input }} uppercase" placeholder="Country code, e.g. GB">
                        </div>
                    </div>

                    <div>
                        <label class="{{ $label }}">Social profiles</label>
                        <textarea name="organization_same_as" rows="3" class="{{ $input }}" placeholder="https://www.instagram.com/watchmarket&#10;https://www.facebook.com/watchmarket">{{ $sameAs }}</textarea>
                        <p class="{{ $hint }}">One URL per line. Helps Google link your social accounts to the business.</p>
                    </div>
                </div>
            </section>

            {{-- Website --}}
            <section class="bg-white rounded-lg shadow-sm p-6 space-y-4" x-data="{ on: {{ $checked('website_enabled') ? 'true' : 'false' }} }">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Website</h3>
                        <p class="{{ $hint }}">Site name and search box. Output on the home page.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer shrink-0">
                        <input type="checkbox" name="website_enabled" value="1" x-model="on" class="rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                        Enabled
                    </label>
                </div>
                <div x-show="on" class="space-y-4">
                    <div>
                        <label class="{{ $label }}">Site name</label>
                        <input type="text" name="website_name" value="{{ $val('website_name') }}" class="{{ $input }}" placeholder="Same as organisation name">
                    </div>
                    <label class="flex items-start gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="website_search_enabled" value="1" @checked($checked('website_search_enabled')) class="mt-0.5 rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                        <span>Include site search <span class="block {{ $hint }} mt-0">Tells Google that watch searches go to {{ route('market.index') }}?q=…</span></span>
                    </label>
                </div>
            </section>

            {{-- Listings --}}
            <section class="bg-white rounded-lg shadow-sm p-6 space-y-4" x-data="{ on: {{ $checked('product_enabled') ? 'true' : 'false' }} }">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Watch listings</h3>
                        <p class="{{ $hint }}">Product, price and availability for each listing page. Filled in automatically from the advert.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer shrink-0">
                        <input type="checkbox" name="product_enabled" value="1" x-model="on" class="rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                        Enabled
                    </label>
                </div>
                <div x-show="on" class="space-y-4">
                    <div>
                        <p class="{{ $label }}">Conditions shown to Google as "new"</p>
                        <div class="flex flex-wrap gap-x-5 gap-y-2">
                            @forelse($conditionOptions as $condition)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="product_new_conditions[]" value="{{ $condition }}" @checked(in_array($condition, $newConditions, true)) class="rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                                    {{ $condition }}
                                </label>
                            @empty
                                <p class="{{ $hint }}">No condition options set up yet.</p>
                            @endforelse
                        </div>
                        <p class="{{ $hint }}">All other conditions are marked as "used".</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="product_include_specs" value="1" @checked($checked('product_include_specs')) class="rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                        Include watch specifications (year, movement, case, dial, box &amp; papers)
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="product_include_seller" value="1" @checked($checked('product_include_seller')) class="rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                        Include seller name (private sellers show their username only)
                    </label>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer pt-2 border-t border-gray-100">
                    <input type="checkbox" name="breadcrumbs_enabled" value="1" @checked($checked('breadcrumbs_enabled')) class="rounded border-gray-300 text-gray-900 focus:ring-gray-800">
                    Breadcrumbs on listing pages (Home › Watches › Brand › Listing)
                </label>
            </section>

            {{-- Custom --}}
            <section class="bg-white rounded-lg shadow-sm p-6 space-y-3">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Custom JSON-LD <span class="text-xs font-normal text-gray-500">(advanced)</span></h3>
                    <p class="{{ $hint }}">Extra schema added to every page, e.g. an FAQ. Must be valid JSON: one object, or a list of objects. Leave blank if unsure.</p>
                </div>
                <textarea name="custom_jsonld" rows="8" spellcheck="false" class="{{ $input }} font-mono text-xs" placeholder='{"@@context": "https://schema.org", "@@type": "...", ...}'>{{ $val('custom_jsonld') }}</textarea>
            </section>

            <div>
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
                    Save Schema Settings
                </button>
            </div>
        </form>

        {{-- Preview --}}
        <aside class="xl:col-span-2 xl:sticky xl:top-6 space-y-3">
            <div class="bg-white rounded-lg shadow-sm p-5">
                <h3 class="text-base font-semibold text-gray-800">Current output</h3>
                <p class="{{ $hint }}">What search engines see now, based on your saved settings.@if(!$sampleAdvert) There are no live listings to preview the product markup.@endif</p>

                <div class="mt-4 space-y-3">
                    @forelse($preview as $title => $data)
                        <details class="border border-gray-200 rounded" @if($loop->first) open @endif>
                            <summary class="cursor-pointer px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50">{{ $title }}</summary>
                            <pre class="p-3 text-[11px] leading-relaxed overflow-x-auto max-h-96 text-gray-800">{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                        </details>
                    @empty
                        <p class="text-sm text-gray-500">All schema markup is switched off.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
