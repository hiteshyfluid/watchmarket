<?php

namespace App\Support;

use App\Models\Advert;
use Illuminate\Support\Str;

/**
 * Builds schema.org JSON-LD for search engines from the admin's Schema
 * Markup settings (SchemaSettings). Each builder returns null when that
 * block is switched off. Render with the `partials.json-ld` view, which
 * escapes the output safely.
 */
class StructuredData
{
    public static function organization(): ?array
    {
        $s = SchemaSettings::all();
        if (!$s['organization_enabled']) {
            return null;
        }

        $address = self::clean([
            '@type' => 'PostalAddress',
            'streetAddress' => $s['organization_street'],
            'addressLocality' => $s['organization_locality'],
            'addressRegion' => $s['organization_region'],
            'postalCode' => $s['organization_postcode'],
            'addressCountry' => $s['organization_country'],
        ]);

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => $s['organization_type'] ?: 'Organization',
            '@id' => url('/') . '/#organization',
            'name' => $s['organization_name'] ?: 'WatchMarket',
            'legalName' => $s['organization_legal_name'],
            'description' => $s['organization_description'],
            'url' => route('home'),
            'logo' => SchemaSettings::logoUrl(),
            'email' => $s['organization_email'],
            'telephone' => $s['organization_phone'],
            // An address with only a country isn't worth publishing.
            'address' => count($address) > 2 ? $address : null,
            'vatID' => $s['organization_vat_id'],
            'sameAs' => array_values(array_filter((array) $s['organization_same_as'])),
            'contactPoint' => self::clean([
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'url' => route('contact.show'),
                'email' => $s['organization_email'],
                'telephone' => $s['organization_phone'],
                'areaServed' => $s['organization_country'] ?: 'GB',
                'availableLanguage' => 'en',
            ]),
        ]);
    }

    public static function website(): ?array
    {
        $s = SchemaSettings::all();
        if (!$s['website_enabled']) {
            return null;
        }

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/') . '/#website',
            'name' => $s['website_name'] ?: ($s['organization_name'] ?: 'WatchMarket'),
            'url' => route('home'),
            'publisher' => $s['organization_enabled'] ? ['@id' => url('/') . '/#organization'] : null,
            'potentialAction' => $s['website_search_enabled'] ? [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('market.index') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ] : null,
        ]);
    }

    /** Product + Offer for a watch listing. Expects the show page's relations loaded. */
    public static function product(Advert $advert): ?array
    {
        $s = SchemaSettings::all();
        if (!$s['product_enabled']) {
            return null;
        }

        $url = route('market.show', $advert);
        $images = array_values(array_filter(array_merge(
            [$advert->mainImageUrl()],
            $advert->images->map(fn ($img) => $img->url())->all()
        )));
        $conditionName = $advert->condition?->name;
        $newConditions = array_map(fn ($c) => Str::lower((string) $c), (array) $s['product_new_conditions']);
        $itemCondition = in_array(Str::lower((string) $conditionName), $newConditions, true)
            ? 'https://schema.org/NewCondition'
            : 'https://schema.org/UsedCondition';
        $seller = $advert->user;

        $specs = $s['product_include_specs'] ? [
            'Year' => $advert->year?->name,
            'Condition' => $conditionName,
            'Movement' => $advert->movement?->name,
            'Case size' => $advert->case_size_mm ? $advert->case_size_mm . 'mm' : $advert->caseDiameter?->name,
            'Case material' => $advert->caseMaterial?->name,
            'Bracelet material' => $advert->braceletMaterial?->name,
            'Dial colour' => $advert->dialColour?->name,
            'Water resistance' => $advert->waterproof?->name,
            'Box' => $advert->box?->name,
            'Papers' => $advert->paper?->name,
        ] : [];

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            '@id' => $url . '#product',
            'name' => $advert->title,
            'url' => $url,
            'image' => $images,
            'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $advert->description))), 5000, '…'),
            'brand' => $advert->brand ? ['@type' => 'Brand', 'name' => $advert->brand->name] : null,
            'model' => $advert->model?->name,
            'mpn' => $advert->reference_number,
            'category' => 'Watches',
            'additionalProperty' => collect($specs)
                ->filter()
                ->map(fn ($value, $name) => ['@type' => 'PropertyValue', 'name' => $name, 'value' => $value])
                ->values()
                ->all(),
            'offers' => self::clean([
                '@type' => 'Offer',
                'url' => $url,
                'price' => number_format((float) $advert->price, 2, '.', ''),
                'priceCurrency' => 'GBP',
                'itemCondition' => $itemCondition,
                'availability' => $advert->is_sold ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
                'priceValidUntil' => $advert->expiry_date?->toDateString(),
                'areaServed' => 'GB',
                'seller' => ($s['product_include_seller'] && $seller) ? [
                    '@type' => $seller->isTradeSeller() ? 'Organization' : 'Person',
                    'name' => $seller->publicDisplayName(),
                ] : null,
            ]),
        ]);
    }

    public static function listingBreadcrumbs(Advert $advert): ?array
    {
        if (!SchemaSettings::get('breadcrumbs_enabled')) {
            return null;
        }

        $items = [
            ['Home', route('home')],
            ['Watches', route('market.index')],
        ];
        if ($advert->brand) {
            $items[] = [$advert->brand->name, route('market.index', ['brand' => $advert->brand->name])];
        }
        $items[] = [$advert->title, route('market.show', $advert)];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item[0],
                'item' => $item[1],
            ])->all(),
        ];
    }

    /** The admin's custom JSON-LD (an object or a list of objects), if any. */
    public static function custom(): ?array
    {
        $decoded = json_decode((string) SchemaSettings::get('custom_jsonld'), true);

        return is_array($decoded) && $decoded !== [] ? $decoded : null;
    }

    /** Drop empty values so the output only contains fields that have data. */
    private static function clean(array $data): array
    {
        return array_filter($data, fn ($v) => $v !== null && $v !== '' && $v !== []);
    }
}
