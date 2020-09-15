<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\FindologicBundle\Domain\SearchRequest;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class Mapper
{
    public function queryToRequest(ProductQuery $query): SearchRequest
    {
        $parameters = [
            'query' => $query->query,
            'first' => $query->cursor ?? $query->offset ?? null,
            'count' => $query->limit,
            'order' => $this->getSortAttributesForRequest($query),
            'attributes' => $this->getAttributesForRequest($query),
        ];

        return new SearchRequest($parameters);
    }

    /**
     * @param ProductQuery $query
     * @return array
     */
    public function getAttributesForRequest(ProductQuery $query): array
    {
        $filterAttributes = $this->getFiltersForRequest($query);
        $facetAttributes = $this->getFacetsForRequest($query);

        return array_merge_recursive(
            $query->category === null ? [] : [
                'cat' => [$query->category],
            ],
            $filterAttributes,
            $facetAttributes
        );
    }

    public function getSortAttributesForRequest(ProductQuery $query): ?string
    {
        if (empty($query->sortAttributes)) {
            return null;
        }

        $fields = array_keys($query->sortAttributes);

        return sprintf(
            '%s dynamic %s',
            $fields[0],
            ($query->sortAttributes[$fields[0]] === ProductQuery::SORT_ORDER_ASCENDING ? 'ASC' : 'DESC')
        );
    }

    /**
     * @return Product[]
     */
    public function dataToProducts(array $items, ProductQuery $query): array
    {
        $locale = Locale::createFromPosix($query->locale);
        $currency = $locale->currency;

        return array_map(
            function ($item) use ($currency, $query) {
                return new Product(
                    [
                        'productId' => $item['id'],
                        'name' => $item['name'],
                        'slug' => $this->getSlugFromUrl($item['url']),
                        'description' => $item['summary'],
                        'categories' => $item['attributes']['cat'],
                        'variants' => empty($item['variants'])
                            ? $this->dataToVariants($query, [$item], $item['id'], $currency)
                            : $this->dataToVariants($query, $item['variants'], $item['id'], $currency),
                        'dangerousInnerProduct' => $query->loadDangerousInnerData ? $item : null,
                    ]
                );
            },
            $items
        );
    }

    /**
     * @return Result\Facet[]
     */
    public function dataToFacets(array $filterData, ProductQuery $query): array
    {
        $filterData = array_merge($filterData['main'], $filterData['other']);
        $queryFilterHandles = array_map(
            function ($queryFilter) {
                return $queryFilter->handle;
            },
            $query->filter
        );

        /**
         * Filters and Facets are merged into the same data structure for Findologic queries. Remove attributes applied
         * from Query filters here to not expose those back as Facets to the user.
         */
        $facetsData = array_filter(
            $filterData,
            function ($filter) use ($queryFilterHandles) {
                return !in_array($filter['name'], $queryFilterHandles);
            }
        );

        return array_map(
            function ($facetData) {
                return $this->dataToFacet($facetData);
            },
            $facetsData
        );
    }

    public function dataToFacet(array $facetData): Result\Facet
    {
        $type = $facetData['type'];

        /**
         * Available filter types are: select, range-slider, color, label, image
         */
        switch ($type) {
            case 'color':
            case 'select':
                $terms = [];
                $selectedTerms = 0;

                foreach ($facetData['values'] as $termData) {
                    $terms[] = new Result\Term(
                        [
                            'handle' => $termData['value'],
                            'name' => $termData['value'],
                            'value' => $termData['value'],
                            'count' => $termData['frequency'],
                            'selected' => $termData['selected'],
                        ]
                    );

                    if ($termData['selected']) {
                        $selectedTerms++;
                    }
                }

                return new Result\TermFacet(
                    [
                        'handle' => $facetData['name'],
                        'key' => $facetData['name'],
                        'terms' => $terms,
                        'selected' => $selectedTerms > 0,
                    ]
                );
            case 'range-slider':
                $facetValues = [
                    'handle' => $facetData['name'],
                    'key' => $facetData['name'],
                    'min' => $this->transformValueToResult($facetData['totalRange']['min'], $facetData['name']),
                    'max' => $this->transformValueToResult($facetData['totalRange']['max'], $facetData['name']),
                    'step' => $this->transformValueToResult($facetData['stepSize'], $facetData['name'])
                ];

                if (isset($facetData['selectedRange'])) {
                    $facetValues['selected'] = true;
                    $facetValues['value'] = [
                        'min' => $this->transformValueToResult($facetData['selectedRange']['min'], $facetData['name']),
                        'max' => $this->transformValueToResult($facetData['selectedRange']['max'], $facetData['name']),
                    ];
                }

                return new Result\RangeFacet($facetValues);
            case 'image':
            case 'label':
                throw new \RuntimeException('ResultFacet of type ' . $type . ' is not yet implemented.');
            default:
                throw new \RuntimeException('ResultFacet of type '. $type . ' is not supported.');
        }
    }

    private function getSlugFromUrl(string $url): string
    {
        $urlWithoutQueryString = strtok($url, '?');

        preg_match('/[^\/]+(?=\/$|$)/', $urlWithoutQueryString, $matches);

        return $matches[0];
    }

    /**
     * @return Variant[]
     */
    private function dataToVariants(ProductQuery $query, array $variants, string $itemId, string $currency): array
    {
        return array_map(
            function ($variant) use ($query, $itemId, $currency) {
                return new Variant(
                    [
                        'id' => $variant['id'],
                        'sku' => current($variant['ordernumbers']),
                        'groupId' => $itemId,
                        'price' => $this->transformValueToResult($variant['price'], 'price'),
                        'currency' => $currency,
                        'attributes' => $variant['attributes'],
                        'images' => [$variant['imageUrl']],
                        'dangerousInnerVariant' => $query->loadDangerousInnerData ? $variant : null,
                    ]
                );
            },
            $variants
        );
    }

    private function getFacetsForRequest(ProductQuery $query): array
    {
        $attributes = [];

        foreach ($query->facets as $facet) {
            if ($facet instanceof TermFacet) {
                $attributes[$facet->handle] = $facet->terms;
            } elseif ($facet instanceof RangeFacet) {
                $attributes[$facet->handle] = [
                    'min' => $this->transformValueToQuery($facet->min, $facet->handle),
                    'max' => $this->transformValueToQuery($facet->max, $facet->handle),
                ];
            } else {
                throw new \RuntimeException('Unsupported facet type ' . get_class($facet));
            }
        }

        return $attributes;
    }

    private function getFiltersForRequest(ProductQuery $query): array
    {
        $attributes = [];

        foreach ($query->filter as $filter) {
            if ($filter instanceof TermFilter) {
                $attributes[$filter->handle] = $filter->terms;
            } elseif ($filter instanceof RangeFilter) {
                $attributes[$filter->handle] = [
                    'min' => $this->transformValueToQuery($filter->min, $filter->handle),
                    'max' => $this->transformValueToQuery($filter->max, $filter->handle),
                ];
            } else {
                throw new \RuntimeException('Unsupported filter type ' . get_class($filter));
            }
        }

        return $attributes;
    }

    private function transformValueToResult($value, string $valueKey)
    {
        if ($valueKey === 'price') {
            return intval($value * 100);
        }

        return $value;
    }

    private function transformValueToQuery($value, string $valueKey)
    {
        if ($valueKey === 'price') {
            return (float) $value / 100;
        }

        return $value;
    }
}
