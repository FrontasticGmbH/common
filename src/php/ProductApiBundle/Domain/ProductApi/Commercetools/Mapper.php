<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Filter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

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
        $lastModified = $productData['lastModifiedAt'] ?? null;
        $version = $productData['version'] ?? 0;

        if (isset($productData['masterData']['current'])) {
            $productId = $productData['id'];
            $productKey = $productData['key'] ?? null;

            $productData = $productData['masterData']['current'];
            $productData['id'] = $productId;
            if ($productKey !== null) {
                $productData['key'] = $productKey;
            }
        }

        $locale = Locale::createFromPosix($query->locale);
        return new Product([
            'productId' => $productData['id'],
            'lastModifiedAt' => $lastModified,
            'version' => $version,
            'name' => $this->getLocalizedValue($locale, $productData['name'] ?? []),
            'slug' => $this->getLocalizedValue($locale, $productData['slug'] ?? []),
            'description' => $this->getLocalizedValue($locale, $productData['description'] ?? []),
            'categories' => array_map(
                function (array $category) {
                    return $category['id'];
                },
                $productData['categories']
            ),
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

        $isOnStock = null;
        if (isset($variantData['availability'])) {
            $availability = $variantData['availability'];
            if (isset($availability['channels'])) {
                // Use first channel for now
                $availability = reset($availability['channels']);
            }
            $isOnStock = $availability['isOnStock'];
        }

        return new Variant([
            'id' => $variantData['id'],
            'sku' => $variantData['sku'] ?? null,
            'groupId' => $groupId,
            'price' => $price,
            'discountedPrice' => $discountedPrice,
            'discounts' => $variantData['discountedPrice']['includedDiscounts'] ?? [],
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
            'isOnStock' => $isOnStock,
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
        if ($facetData['type'] === 'terms' && $facetData['dataType'] === 'number') {
            return $this->dataToNumberRangeFacet($facetKey, $facetData, $facetQuery);
        }

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

    /**
     * Special handling for number facets due to CommerceTools not coping with (* to *).
     *
     * In some cases, CommerceTools cannot cope with a facet query (NOT filter!) of (* to *). CT support claimed this
     * is intentional and we should use term facet for the query. Since the facet FILTER should be a range one, we
     * are doing this nice conversion.
     *
     * @param string $facetKey
     * @param array $facetData
     * @param Query\RangeFacet $facetQuery
     * @return ProductApi\Result\RangeFacet
     */
    public function dataToNumberRangeFacet(
        string $facetKey,
        array $facetData,
        ProductApi\Query\RangeFacet $facetQuery = null
    ): ProductApi\Result\RangeFacet {
        $min = \PHP_INT_MAX;
        $max = \PHP_INT_MIN;

        foreach ($facetData['terms'] as $term) {
            $value = (float)$term['term'];
            $min = min($min, $value);
            $max = max($max, $value);
        }

        $min = min($min, $max);
        $max = max($min, $max);

        $facetValues = [
            'handle' => $facetKey,
            'key' => $facetKey,
            'min' => $min,
            'max' => $max,
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
        // We rely on CommerceTools price selection

        if (isset($variantData['scopedPrice'])) {
            return [
                $variantData['scopedPrice']['currentValue']['centAmount'],
                $variantData['scopedPrice']['currentValue']['currencyCode'],
                ($variantData['scopedPriceDiscounted']
                    ? $variantData['scopedPrice']['value']['centAmount']
                    : null),
            ];
        }

        if (isset($variantData['price'])) {
            return [
                $variantData['price']['value']['centAmount'],
                $variantData['price']['value']['currencyCode'],
                (isset($variantData['price']['discounted']['value']['centAmount'])
                    ? $variantData['price']['discounted']['value']['centAmount'] ?? null
                    : null),
            ];
        }

        return [null, null, null];
    }

    public function dataToAttributes(array $variantData, Locale $locale): array
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
                    return $this->extractAttributeValue($attribute['value'], $locale);
                },
                $variantData['attributes']
            )
        ));
    }

    private function extractAttributeValue($attributeValue, Locale $locale)
    {
        if (isset($attributeValue['centAmount'])) {
            return $attributeValue;
        }

        if (isset($attributeValue['key']) && isset($attributeValue['label'])) {
            return [
                'key' => $attributeValue['key'],
                'label' => $this->extractAttributeValue($attributeValue['label'], $locale),
            ];
        }

        if (is_array($attributeValue) && $this->isNumericArray($attributeValue)) {
            return array_map(
                function ($collectionValue) use ($locale) {
                    return $this->extractAttributeValue($collectionValue, $locale);
                },
                $attributeValue
            );
        }

        if (is_array($attributeValue) && !$this->isNumericArray($attributeValue)) {
            return $this->getLocalizedValue($locale, $attributeValue ?? []);
        }

        return $attributeValue;
    }

    private function isNumericArray(array $array): bool
    {
        return ($array === array_values($array));
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
                    $facet = sprintf('%s', $facetDefinition['attributeId']);
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

        return array_values(array_unique($facets));
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
            switch ($typeLookup[$facet->handle] ?? '__UNKNOWN') {
                case 'money':
                    $filters[] = sprintf('%s.centAmount:range (%s to %s)', $facet->handle, $facet->min, $facet->max);
                    break;

                case 'enum':
                    $filters[] = sprintf('%s.label:%s', $facet->handle, $this->termsToLogicalOrString($facet->terms));
                    break;

                case 'localizedEnum':
                    $filters[] = sprintf(
                        '%s.label.%s:%s',
                        $facet->handle,
                        $locale->language,
                        $this->termsToLogicalOrString($facet->terms)
                    );
                    break;

                case 'localizedText':
                    $filters[] = sprintf(
                        '%s.%s:%s',
                        $facet->handle,
                        $locale->language,
                        $this->termsToLogicalOrString($facet->terms)
                    );
                    break;

                case 'number':
                case 'boolean':
                case 'text':
                case 'reference':
                default:
                    if ($facet instanceof Query\TermFacet) {
                        $filters[] = sprintf(
                            '%s:%s',
                            $facet->handle,
                            $this->termsToLogicalOrString($facet->terms)
                        );
                    } else {
                        $filters[] = sprintf('%s:range (%s to %s)', $facet->handle, $facet->min, $facet->max);
                    }
                    break;
            }
        }
        return $filters;
    }

    /**
     * @param Query\Filter[]|string[] $filter
     * @return string[]
     */
    public function prepareQueryFilter(array $filter, Locale $locale): array
    {
        $preparedFilter = [];
        foreach ($filter as $queryFilter) {
            if (is_string($queryFilter)) {
                // BC for original usage of this query field
                $preparedFilter[] = $queryFilter;
                continue;
            }

            $preparedFilter[] = $this->toFilterString($queryFilter, $locale);
        }
        return $preparedFilter;
    }

    /**
     * Filter handling is similar to facet handling, but not locale aware and cannot rely on facet config lookup.
     *
     * @param Filter $queryFilter
     * @return string
     */
    private function toFilterString(Filter $queryFilter, Locale $locale): string
    {
        switch ($queryFilter->attributeType) {
            case 'money':
                // Only range filters valid for money
                return sprintf(
                    '%s.centAmount:range (%s to %s)',
                    $queryFilter->handle,
                    $queryFilter->min ?? '*',
                    $queryFilter->max ?? '*'
                );
                break;

            case 'enum':
            case 'localizedEnum':
                return sprintf(
                    '%s.key:%s',
                    $queryFilter->handle,
                    // Only term filters valid for (l)enum
                    $this->termsToLogicalOrString($queryFilter->terms ?? [])
                );
                break;

            case 'localizedText':
                return sprintf(
                    '%s.%s:%s',
                    $queryFilter->handle,
                    $locale->language,
                    $this->termsToLogicalOrString($queryFilter->terms ?? [])
                );
                break;

            case 'number':
            case 'boolean':
            case 'text':
            case 'reference':
            default:
                // Default handling below
                break;
        }

        if ($queryFilter instanceof Query\TermFilter) {
            return sprintf(
                '%s:%s',
                $queryFilter->handle,
                $this->termsToLogicalOrString($queryFilter->terms)
            );
        } else {
            return sprintf(
                '%s:range (%s to %s)',
                $queryFilter->handle,
                $queryFilter->min ?? '*',
                $queryFilter->max ?? '*'
            );
        }
    }

    private function termsToLogicalOrString(array $terms): string
    {
        return implode(
            ",",
            array_map(
                function ($term) {
                    return sprintf('"%s"', $term);
                },
                $terms
            )
        );
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
