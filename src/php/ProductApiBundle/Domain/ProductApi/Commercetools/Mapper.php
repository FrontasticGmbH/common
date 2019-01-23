<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;

/**
 * @SuppressWarnings(PHPMD) TODO: Refactor or add more tests
 */
class Mapper
{
    private $localeOverwrite;

    public function __construct($localeOverwrite = null)
    {
        $this->localeOverwrite = $localeOverwrite;
    }

    public function dataToProduct(array $productData, ProductQuery $query): Product
    {
        if (isset($productData['masterData']['current'])) {
            $productId = $productData['id'];
            $productData = $productData['masterData']['current'];
            $productData['id'] = $productId;
        }

        $locale = Locale::createFromPosix($query->locale);
        return new Product([
            'productId' => $productData['id'],
            'version' => $productData['version'] ?? 0,
            'name' => $this->getLocalizedValue($locale, $productData['name'] ?? []),
            'slug' => $this->getLocalizedValue($locale, $productData['slug'] ?? []),
            'description' => $this->getLocalizedValue($locale, $productData['description'] ?? []),
            'categories' => array_map(function (array $category) {
                return $category['id'];
            }, $productData['categories']),
            'variants' => $this->dataToVariants($productData, $query, $locale),
            'dangerousInnerProduct' => $this->dataToDangerousInnerData($productData, $query),
        ]);
    }

    public function dataToVariants(array $productData, ProductQuery $query, Locale $locale): array
    {
        $variants = [$this->dataToVariant($productData['masterVariant'], $query, $locale)];
        foreach ($productData['variants'] as $variantData) {
            $variants[] = $this->dataToVariant($variantData, $query, $locale);
        }
        return $variants;
    }

    /**
     * @param array $variantData
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query $query
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale $locale
     * @return \Frontastic\Common\ProductApiBundle\Domain\Variant
     */
    public function dataToVariant(array $variantData, Query $query, Locale $locale): Variant
    {
        list($price, $currency, $discountedPrice) = $this->dataToPrice($variantData, $locale);

        $attributes = $this->dataToAttributes($variantData, $locale);
        $groupId = $attributes['baseId'];

        return new Variant([
            'id' => $variantData['id'],
            'sku' => $variantData['sku'] ?? null,
            'groupId' => $groupId,
            'price' => $price,
            'discountedPrice' => $discountedPrice,
            // @TODO: Currency should only be stored in context. Property should be removed.
            'currency' => $currency,
            'attributes' => $attributes,
            'images' => array_merge(
                array_map(
                    function (array $asset): string {
                        return $asset['sources'][0]['uri'];
                    },
                    $variantData['assets']
                ),
                array_map(
                    function (array $image): string {
                        return $image['url'];
                    },
                    $variantData['images']
                )
            ),
            /* @todo Evaluate when availability is set and when not */
            'isOnStock' => (isset($variantData['availability']) ? $variantData['availability']['isOnStock'] : true),
            'dangerousInnerVariant' => $this->dataToDangerousInnerData($variantData, $query),
        ]);
    }

    public function dataToFacets(array $facetsData, ProductQuery $query): array
    {
        $facets = [];
        foreach ($facetsData as $facetKey => $facetData) {
            $facets[] = $this->dataToFacet(
                $facetKey,
                $facetData,
                $this->findFacetQuery($query, $facetKey)
            );
        }
        return $facets;
    }

    private function findFacetQuery(ProductQuery $query, string $facetKey): ?ProductApi\Query\Facet
    {
        foreach ($query->facets as $facetQuery) {
            if ($facetQuery->handle === $facetKey) {
                return $facetQuery;
            }
        }
        return null;
    }

    public function dataToFacet(
        $facetKey,
        array $facetData,
        ProductApi\Query\Facet $facetQuery = null
    ): ProductApi\Result\Facet {
        switch ($facetData['type']) {
            case 'range':
                return $this->dataToRangeFacet($facetKey, $facetData, $facetQuery);

            case 'terms':
                return $this->dataToTermFacet($facetKey, $facetData, $facetQuery);

            default:
                throw new \RuntimeException('Unknown facet type ' . $facetData['type']);
        }
    }

    public function dataToTermFacet(
        string $facetKey,
        array $facetData,
        ProductApi\Query\TermFacet $facetQuery = null
    ): ProductApi\Result\TermFacet {

        $selectedTermsMap = [];
        if ($facetQuery !== null) {
            $selectedTermsMap = array_fill_keys($facetQuery->terms, true);
        }

        $terms = [];
        foreach ($facetData['terms'] as $termData) {
            $terms[] = new Term([
                'handle' => $termData['term'],
                'name' => $termData['term'],
                'value' => $termData['term'],
                'count' => $termData['count'],
                'selected' => isset($selectedTermsMap[$termData['term']]),
            ]);
        }

        return new ProductApi\Result\TermFacet([
            'handle' => $facetKey,
            'key' => $facetKey,
            'terms' => $terms,
            'selected' => (count($selectedTermsMap) > 0),
        ]);
    }

    public function dataToRangeFacet(
        string $facetKey,
        array $facetData,
        ProductApi\Query\RangeFacet $facetQuery = null
    ): ProductApi\Result\RangeFacet {
        $facetValues = [
            'handle' => $facetKey,
            'key' => $facetKey,
            'min' => $facetData['ranges'][0]['min'],
            'max' => $facetData['ranges'][0]['max'],
        ];

        if ($facetQuery !== null) {
            $facetValues['selected'] = true;
            $facetValues['value'] = [
                'min' => $facetQuery->min,
                'max' => $facetQuery->max,
            ];
        }

        return new ProductApi\Result\RangeFacet($facetValues);
    }

    public function dataToPrice(array $variantData, Locale $locale): array
    {
        // @todo: Fallback if no default price exists?
        $default = [-1, 'EUR', null];

        foreach ($variantData['prices'] as $price) {
            /* @TODO: Do we support customer group related prices? */
            if (isset($price['customerGroup'])) {
                continue;
            }
            if ($locale->currency !== $price['value']['currencyCode']) {
                continue;
            }
            if (isset($price['country']) && $locale->territory === $price['country']) {
                return [
                    $price['value']['centAmount'],
                    $price['value']['currencyCode'],
                    (isset($price['discounted']) ? $price['discounted']['value']['centAmount'] : null)
                ];
            }
            $default = [
                $price['value']['centAmount'],
                $price['value']['currencyCode'],
                (isset($price['discounted']) ? $price['discounted']['value']['centAmount'] : null)
            ];
        }
        return $default;
    }

    public function dataToAttributes(array $variantData, $locale): array
    {
        return array_merge(['baseId' => null], array_combine(
            array_map(
                function (array $attribute): string {
                    return $attribute['name'];
                },
                $variantData['attributes']
            ),
            array_map(
                function (array $attribute) use ($locale) {
                    if (is_array($attribute['value'])) {
                        return $this->getLocalizedValue($locale, $attribute['value'] ?? []);
                    }
                    return $attribute['value'];
                },
                $variantData['attributes']
            )
        ));
    }

    public function dataToDangerousInnerData(array $rawData, Query $query): ?array
    {
        if ($query->loadDangerousInnerData) {
            return $rawData;
        }
        return null;
    }

    private function getLocalizedValue(Locale $locale, array $localizedString)
    {
        $commercetoolsLocale = str_replace('_', '-', $locale->original);
        if (isset($localizedString[$this->localeOverwrite])) {
            return $localizedString[$this->localeOverwrite];
        } elseif (isset($localizedString[$commercetoolsLocale])) {
            return $localizedString[$commercetoolsLocale];
        } elseif (isset($localizedString[$locale->language])) {
            return $localizedString[$locale->language];
        } elseif (isset($localizedString['key'])) {
            return $localizedString['key'];
        } else {
            return reset($localizedString) ?: '';
        }
    }

    /**
     * Converts the facets defined in {@see $this->options} to queryable format.
     *
     * @param array $facetDefinitions
     * @param Locale $locale
     * @return string[]
     */
    public function facetsToRequest(array $facetDefinitions, Locale $locale): array
    {
        $facets = [];
        foreach ($facetDefinitions as $facetDefinition) {
            $facet = '';
            switch ($facetDefinition['attributeType']) {
                case 'number':
                    $facet = sprintf('%s:range (* to *)', $facetDefinition['attributeId']);
                    break;

                case 'money':
                    $facet = sprintf('%s.centAmount:range (0 to *)', $facetDefinition['attributeId']);
                    break;

                case 'enum':
                    $facet = sprintf('%s.label', $facetDefinition['attributeId']);
                    break;

                case 'localizedEnum':
                    $facet = sprintf('%s.label.%s', $facetDefinition['attributeId'], $locale->language);
                    break;

                case 'localizedText':
                    $facet = sprintf('%s.%s', $facetDefinition['attributeId'], $locale->language);
                    break;

                case 'boolean':
                case 'text':
                case 'reference':
                default:
                    $facet = $facetDefinition['attributeId'];
                    break;
            }
            // Alias to identifier used by us
            $facets[] = sprintf('%s as %s', $facet, $facetDefinition['attributeId']);
        }

        return array_unique($facets);
    }

    /**
     * @param ProductApi\Query\Facet[] $facets
     * @param array $facetDefinitions
     * @param Locale $locale
     * @return string[]
     */
    public function facetsToFilter(array $facets, array $facetDefinitions, Locale $locale): array
    {
        $typeLookup = $this->attributeTypeLookup($facetDefinitions);

        $filters = [];
        foreach ($facets as $facet) {
            switch ($typeLookup[$facet->handle]) {
                case 'money':
                    $filters[] = sprintf('%s.centAmount:range (%s to %s)', $facet->handle, $facet->min, $facet->max);
                    break;

                case 'enum':
                    foreach ($facet->terms as $term) {
                        $filters[] = sprintf('%s.label:"%s"', $facet->handle, $term);
                    }
                    break;

                case 'localizedEnum':
                    foreach ($facet->terms as $term) {
                        $filters[] = sprintf('%s.label.%s:"%s"', $facet->handle, $locale->language, $term);
                    }
                    break;

                case 'localizedText':
                    foreach ($facet->terms as $term) {
                        $filters[] = sprintf('%s.%s:"%s"', $facet->handle, $locale->language, $term);
                    }
                    break;

                case 'number':
                case 'boolean':
                case 'text':
                case 'reference':
                default:
                    if ($facet instanceof Query\TermFacet) {
                        foreach ($facet->terms as $term) {
                            $filters[] = sprintf('%s:"%s"', $facet->handle, $term);
                        }
                    } else {
                        $filters[] = sprintf('%s:range (%s to %s)', $facet->handle, $facet->min, $facet->max);
                    }
                    break;
            }
        }
        return $filters;
    }

    private function attributeTypeLookup(array $facetDefinitions): array
    {
        $lookup = [];
        foreach ($facetDefinitions as $facetDefinition) {
            $lookup[$facetDefinition['attributeId']] = $facetDefinition['attributeType'];
        }
        return $lookup;
    }
}
