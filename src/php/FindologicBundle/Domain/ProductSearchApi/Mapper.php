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
    private const DEFAULT_OUTPUT_ATTRIBUTES = ['price'];

    /**
     * @var array
     */
    private $outputAttributes;

    /**
     * @var string|null
     */
    private $categoryProperty;

    /**
     * @var string|null
     */
    private $slugProperty;

    /**
     * @var string|null
     */
    private $slugRegex;

    public function __construct(
        array $outputAttributes = [],
        ?string $categoryProperty = null,
        ?string $slugProperty = null,
        ?string $slugRegex = null
    ) {
        $this->categoryProperty = $categoryProperty;
        $this->slugProperty = $slugProperty;
        $this->slugRegex = $slugRegex;
        $this->outputAttributes = $outputAttributes;
    }

    public function queryToRequest(ProductQuery $query): SearchRequest
    {
        $parameters = [
            'query' => $query->query,
            'first' => $query->cursor ?? $query->offset ?? null,
            'count' => $query->limit,
            'order' => $this->getSortAttributesForRequest($query),
            'attributes' => $this->getAttributesForRequest($query),
            'outputAttributes' => array_unique(
                array_merge(self::DEFAULT_OUTPUT_ATTRIBUTES, $this->outputAttributes)
            )
        ];

        $slugPropertySegments = explode('.', $this->slugProperty);

        if (count($slugPropertySegments) > 1 && $slugPropertySegments[0] === 'properties') {
            $parameters['properties'] = [$slugPropertySegments[1]];
        }

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
        $categoryAttributes = $this->getCategoriesForRequest($query);

        return array_merge_recursive(
            $categoryAttributes,
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
                        'name' => $this->stripHtml($item['name']),
                        'slug' => $this->getSlug($item),
                        'description' => $this->stripHtml($item['summary']),
                        'categories' => $this->getCategories($item),
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
            case 'label':
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
            default:
                throw new \RuntimeException('ResultFacet of type '. $type . ' is not supported.');
        }
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

    private function getCategoriesForRequest(ProductQuery $query): array
    {
        $categories = $query->getAllUniqueCategories();
        $attributes = [];

        if(count($categories) >= 1) {
            $attributes['cat'] = [$categories[0]];
            //TODO: warn when count >1
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

    private function stripHtml(string $input): string
    {
        return htmlspecialchars_decode(
            strip_tags(
                preg_replace(
                    '{<br\s*/?>}i',
                    "\n",
                    $input
                )
            )
        );
    }

    /**
     * @return string[]
     */
    private function getCategories(array $item): array
    {
        if ($this->categoryProperty === null) {
            return [];
        }

        return $this->getArrayValue($item, $this->categoryProperty) ?? [];
    }

    /**
     * @return array|mixed|null
     */
    private function getArrayValue(array $item, string $key)
    {
        $array = $item;
        $segments = explode('.', $key);

        foreach ($segments as $segment) {
            if (!isset($array[$segment])) {
                return null;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * @param $item
     * @return string
     */
    private function getSlug($item): string
    {
        if ($this->slugProperty !== null) {
            $slug = $this->getArrayValue($item, $this->slugProperty);
            if ($slug !== null) {
                return $slug;
            }
        }

        return $this->getSlugFromUrl($item['url']);
    }

    private function getSlugFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $matches = [];

        if ($this->slugRegex !== null) {
            preg_match($this->slugRegex, $path, $matches);
        }

        return $matches['url'] ?? $path;
    }
}
