<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\AttributeOption;
use App\Support\SchemaSettings;
use App\Support\StructuredData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SchemaSettingController extends Controller
{
    public function edit(): View
    {
        $settings = SchemaSettings::all();
        $conditionOptions = AttributeOption::query()
            ->where('type', 'condition')
            ->pluck('name')
            ->unique()
            ->values();

        $sampleAdvert = Advert::query()
            ->where('status', Advert::STATUS_ACTIVE)
            ->with(['user', 'brand', 'model', 'images', 'paper', 'box', 'year', 'condition', 'movement',
                'caseMaterial', 'braceletMaterial', 'dialColour', 'caseDiameter', 'waterproof'])
            ->latest()
            ->first();

        $preview = array_filter([
            'Organization (every page)' => StructuredData::organization(),
            'Website (home page)' => StructuredData::website(),
            'Product (listing page' . ($sampleAdvert ? ': ' . $sampleAdvert->title : '') . ')' => $sampleAdvert ? StructuredData::product($sampleAdvert) : null,
            'Breadcrumbs (listing page)' => $sampleAdvert ? StructuredData::listingBreadcrumbs($sampleAdvert) : null,
            'Custom (every page)' => StructuredData::custom(),
        ]);

        return view('admin.schema.edit', compact('settings', 'conditionOptions', 'preview', 'sampleAdvert'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_type' => ['required', Rule::in(SchemaSettings::ORGANIZATION_TYPES)],
            'organization_name' => 'required|string|max:255',
            'organization_legal_name' => 'nullable|string|max:255',
            'organization_description' => 'nullable|string|max:1000',
            'organization_logo' => 'nullable|string|max:500',
            'organization_email' => 'nullable|email|max:255',
            'organization_phone' => 'nullable|string|max:50',
            'organization_street' => 'nullable|string|max:255',
            'organization_locality' => 'nullable|string|max:255',
            'organization_region' => 'nullable|string|max:255',
            'organization_postcode' => 'nullable|string|max:20',
            'organization_country' => 'nullable|string|size:2',
            'organization_vat_id' => 'nullable|string|max:50',
            'organization_same_as' => 'nullable|string|max:3000',
            'website_name' => 'nullable|string|max:255',
            'product_new_conditions' => 'nullable|array',
            'product_new_conditions.*' => 'string|max:100',
            'custom_jsonld' => 'nullable|string|max:20000',
        ], [
            'organization_country.size' => 'Use a 2-letter country code, e.g. GB.',
        ]);

        $sameAs = collect(preg_split('/[\r\n,]+/', (string) ($validated['organization_same_as'] ?? '')))
            ->map(fn ($url) => trim($url))
            ->filter()
            ->unique()
            ->values();
        foreach ($sameAs as $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url)) {
                throw ValidationException::withMessages(['organization_same_as' => "\"{$url}\" is not a valid web address (it must start with https://)."]);
            }
        }

        $customJson = trim((string) ($validated['custom_jsonld'] ?? ''));
        if ($customJson !== '') {
            $decoded = json_decode($customJson, true);
            if (!is_array($decoded)) {
                throw ValidationException::withMessages(['custom_jsonld' => 'Custom JSON-LD is not valid JSON: ' . json_last_error_msg() . '.']);
            }
            $customJson = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $text = fn (string $key) => trim((string) ($validated[$key] ?? ''));

        SchemaSettings::save([
            'organization_enabled' => $request->boolean('organization_enabled'),
            'organization_type' => $validated['organization_type'],
            'organization_name' => $text('organization_name'),
            'organization_legal_name' => $text('organization_legal_name'),
            'organization_description' => $text('organization_description'),
            'organization_logo' => $text('organization_logo'),
            'organization_email' => $text('organization_email'),
            'organization_phone' => $text('organization_phone'),
            'organization_street' => $text('organization_street'),
            'organization_locality' => $text('organization_locality'),
            'organization_region' => $text('organization_region'),
            'organization_postcode' => $text('organization_postcode'),
            'organization_country' => strtoupper($text('organization_country')),
            'organization_vat_id' => $text('organization_vat_id'),
            'organization_same_as' => $sameAs->all(),
            'website_enabled' => $request->boolean('website_enabled'),
            'website_name' => $text('website_name'),
            'website_search_enabled' => $request->boolean('website_search_enabled'),
            'product_enabled' => $request->boolean('product_enabled'),
            'breadcrumbs_enabled' => $request->boolean('breadcrumbs_enabled'),
            'product_new_conditions' => array_values((array) ($validated['product_new_conditions'] ?? [])),
            'product_include_seller' => $request->boolean('product_include_seller'),
            'product_include_specs' => $request->boolean('product_include_specs'),
            'custom_jsonld' => $customJson,
        ]);

        return redirect()->route('admin.schema.edit')->with('success', 'Schema markup settings saved.');
    }
}
