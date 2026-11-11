<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Admin-editable settings for schema.org structured data, stored as one JSON
 * value in site_settings. Missing keys fall back to defaults().
 */
class SchemaSettings
{
    public const KEY = 'schema_settings';

    public const ORGANIZATION_TYPES = ['Organization', 'OnlineStore', 'Corporation', 'LocalBusiness'];

    private static ?array $cache = null;

    public static function defaults(): array
    {
        $vat = preg_replace('/\s+/', '', (string) SiteSetting::getValue('invoice_vat_number'));
        if (ctype_digit($vat)) {
            $vat = 'GB' . $vat;
        }

        return [
            // Organization (every page)
            'organization_enabled' => true,
            'organization_type' => 'Organization',
            'organization_name' => SiteSetting::getValue('invoice_company_name') ?: 'WatchMarket',
            'organization_legal_name' => '',
            'organization_description' => '',
            'organization_logo' => '',
            'organization_email' => '',
            'organization_phone' => '',
            'organization_street' => '',
            'organization_locality' => '',
            'organization_region' => '',
            'organization_postcode' => '',
            'organization_country' => 'GB',
            'organization_vat_id' => $vat,
            'organization_same_as' => [],

            // WebSite (home page)
            'website_enabled' => true,
            'website_name' => '',
            'website_search_enabled' => true,

            // Listings
            'product_enabled' => true,
            'breadcrumbs_enabled' => true,
            'product_new_conditions' => ['New', 'Unworn'],
            'product_include_seller' => true,
            'product_include_specs' => true,

            // Extra JSON-LD output on every page (validated JSON string)
            'custom_jsonld' => '',
        ];
    }

    public static function all(): array
    {
        if (self::$cache === null) {
            $stored = json_decode((string) SiteSetting::getValue(self::KEY, ''), true);
            self::$cache = array_merge(self::defaults(), is_array($stored) ? $stored : []);
        }

        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all()[$key] ?? $default;
    }

    public static function save(array $settings): void
    {
        $settings = array_intersect_key($settings, self::defaults());
        SiteSetting::setValue(self::KEY, json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        self::$cache = null;
    }

    /** Logo URL: the admin's value, or the site logo. Relative paths become absolute. */
    public static function logoUrl(): string
    {
        $logo = trim((string) self::get('organization_logo'));
        if ($logo === '') {
            return asset('images/logo.webp');
        }

        return preg_match('#^https?://#i', $logo) ? $logo : asset(ltrim($logo, '/'));
    }
}
