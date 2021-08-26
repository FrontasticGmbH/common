<?php

namespace Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet as QueryFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet as ResultFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term as ResultTerm;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\TermFacet as ResultTermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\RangeFacet as ResultRangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;

class Mapper
{
    private const PRODUCT_ID_ATTRIBUTE_KEY = 'productId';
    private const SKU_ATTRIBUTE_KEY = 'sku';

    private const IGNORED_ATTRIBUTES = [
        self::PRODUCT_ID_ATTRIBUTE_KEY,
        self::SKU_ATTRIBUTE_KEY,
    ];

    public function dataToProducts(array $data, ProductQuery $query): array
    {
        $products = [];
        foreach ($data as $productData) {
            // When the `distinct` request option is enabled, the response does not contain duplicated
            // products per each variant.
            $products[] = new Product([
                'productId' => $productData['productId'] ?? null,
                'name' => $productData['name'] ?? null,
                'slug' => $productData['slug'] ?? null,
                'description' => $productData['description'] ?? null,
                'categories' => $productData['categories'] ?? [],
                'variants' => [
                    // Algolia always returns a single variant per hit.
                    new Variant([
                        'id' => $productData['variantId'] ?? $productData['productId'] ?? null,
                        'sku' => $productData['sku'] ?? null,
                        'price' => intval($productData['price'] * 100),
                        'discountedPrice' => key_exists('discountedPrice', $productData) ?
                            intval($productData['discountedPrice'] * 100) :
                            null
                        ,
                        'attributes' => $productData, // Store all attributes returned by Algolia.
                        'images' => $productData['images'] ?? [],
                        'isOnStock' => $productData['isOnStock'] ?? null,
                        'dangerousInnerVariant' => $query->loadDangerousInnerData ? $productData : null
                    ])
                ],
                'dangerousInnerProduct' => $query->loadDangerousInnerData ? $productData : null
            ]);
        }
        return $products;
    }

    /**
     * @param array $data
     * @param ProductQuery|null $query
     *
     * @return ResultFacet[]
     */
    public function dataToFacets(array $data, ProductQuery $query = null): array
    {
        $facets = [];

        $facetsData = $data['facets'] ?? [];
        foreach ($facetsData as $facetKey => $facetTerms) {
            if ($this->shouldIgnoreAttributeKey($facetKey)) {
                continue;
            }

            $facetQuery = $query ? $this->findFacetQuery($query, $facetKey) : null;
            $selectedTermsMap = [];
            if ($facetQuery !== null) {
                $selectedTermsMap = array_fill_keys($facetQuery->terms, true);
            }

            $terms = [];
            foreach ($facetTerms as $term => $count) {
                $terms[] = new ResultTerm([
                    'handle' => $term,
                    'name' => $term,
                    'value' => $term,
                    'count' => $count,
                    'selected' => isset($selectedTermsMap[$term]),
                ]);
            }

            $facets[] = new ResultTermFacet([
                'handle' => $facetKey,
                'key' => $facetKey,
                'terms' => $terms,
                'selected' => !empty($selectedTermsMap),
            ]);
        }

        $facetsStatsData = $data['facets_stats'] ?? [];
        foreach ($facetsStatsData as $facetKey => $facetStat) {
            if ($this->shouldIgnoreAttributeKey($facetKey)) {
                continue;
            }

            $facetQuery = $query ? $this->findFacetQuery($query, $facetKey) : null;

            $facetValues = [
                'handle' => $facetKey,
                'key' => $facetKey,
                'min' => $facetStat['min'] ?? null,
                'max' => $facetStat['max'] ?? null,
            ];

            if ($facetQuery !== null) {
                $facetValues['selected'] = true;
                $facetValues['value'] = [
                    'min' => $facetStat['min'],
                    'max' => $facetStat['max'],
                ];
            }

            $facets[] = new ResultRangeFacet($facetValues);
        }

        return $facets;
    }

    public function dataToAttributes(array $settingsResponse, array $searchResponse): array
    {
        $attributes = [];
        foreach ($settingsResponse['attributesForFaceting'] as $searchableAttributeData) {
            $searchableAttributeKey = preg_replace(
                [
                    '/searchable\((.*?)\)/',
                    '/filterOnly\((.*?)\)/',
                ],
                '$1',
                $searchableAttributeData
            );

            if ($this->shouldIgnoreAttributeKey($searchableAttributeKey)) {
                continue;
            }

            $attributes[$searchableAttributeKey] = new Attribute([
                'attributeId' => $searchableAttributeKey,
                'type' => Attribute::TYPE_TEXT, // Use text type as default
            ]);
        }

        foreach ($this->dataToFacets($searchResponse) as $facet) {
            if ($facet instanceof ResultTermFacet) {
                $attributes[$facet->key] = new Attribute([
                    'attributeId' => $facet->key,
                    'type' => Attribute::TYPE_ENUM,
                    'values' => array_map(
                        function ($term) {
                            return [
                                'key' => $term->value,
                                'label' => $term->value,
                            ];
                        },
                        $facet->terms
                    ),
                ]);
            }

            // Only "price" will be returned as a searchable attribute since only "money" type supports range filter
            if ($facet instanceof ResultRangeFacet && $facet->key == 'price') {
                $attributes[$facet->key] = new Attribute([
                    'attributeId' => $facet->key,
                    'type' => Attribute::TYPE_MONEY,
                    'values' => [
                        'min' => $facet->min,
                        'max' => $facet->max,
                    ]
                ]);
            }
        }

        return $attributes;
    }

    protected function findFacetQuery(ProductQuery $query, string $facetKey): ?QueryFacet
    {
        foreach ($query->facets as $facetQuery) {
            if ($facetQuery->handle === $facetKey) {
                return $facetQuery;
            }
        }
        return null;
    }

    protected function shouldIgnoreAttributeKey(string $attributeKey): bool
    {
        return in_array($attributeKey, self::IGNORED_ATTRIBUTES);
    }
}
