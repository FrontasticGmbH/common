<?php

namespace Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi;

use Frontastic\Common\AlgoliaBundle\Domain\AlgoliaClient;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet as ResultFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;

class AlgoliaProductSearchApi extends ProductSearchApiBase
{
    private const PRODUCT_ID_ATTRIBUTE_KEY = 'productId';
    private const SKU_ATTRIBUTE_KEY = 'sku';

    private const IGNORED_ATTRIBUTES = [
        self::PRODUCT_ID_ATTRIBUTE_KEY,
        self::SKU_ATTRIBUTE_KEY,
    ];

    /**
     * @var AlgoliaClient
     */
    private $client;

    /**
     * @var EnabledFacetService
     */
    private $enabledFacetService;

    public function __construct(AlgoliaClient $client, EnabledFacetService $enabledFacetService)
    {
        $this->client = $client;
        $this->enabledFacetService = $enabledFacetService;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        // The index selected should have configured `productId` as "Attribute for Distinct"
        // https://www.algolia.com/doc/guides/managing-results/refine-results/grouping/how-to/item-variations

        // In order to perform filter by `productIds`, `skus`, `productType` or `category` the Algolia index should
        // have those fields set as facets.

        // TODO: implement $query->sortAttributes. This can only be implemented by using another index.

        $queryTerm = $query->query ?? '';

        $requestOptions = [
            'distinct' => true, // Enable the "Attribute for Distinct" to ensure that products are not duplicated.
            'length' => $query->limit,
            'offset' => $query->offset,
            // TODO: use cursor instead of offset
            // 'hitsPerPage' => $query->limit,
            // 'page' => (int)ceil($query->cursor / $query->limit),
        ];

        $filters = [];
        foreach ($query->filter as $queryFilter) {
            if ($queryFilter instanceof TermFilter) {
                $terms = array_map(
                    function ($term) use ($queryFilter) {
                        return $queryFilter->handle . ":'$term'" ;
                    },
                    $queryFilter->terms
                );
                $filters[] = '(' . implode(' OR ', $terms) . ')';
            } elseif ($queryFilter instanceof RangeFilter) {
                list($min, $max) = $this->extractRangeValues($queryFilter);

                $filters[] = sprintf(
                    '%s: %s to %s',
                    $queryFilter->handle,
                    $min,
                    $max
                );
            }
        }

        $requestOptions['filters'] = implode(' AND ', $filters);

        $enabledFacetsIds = [];
        foreach ($this->enabledFacetService->getEnabledFacetDefinitions() as $enabledFacetDefinition) {
            $requestOptions['facets'][] = $enabledFacetDefinition->attributeId;
            $enabledFacetsIds[] = $enabledFacetDefinition->attributeId;
        }

        foreach ($query->facets as $queryFacet) {
            if (!in_array($queryFacet->handle, $enabledFacetsIds)) {
                continue;
            }

            if ($queryFacet instanceof TermFacet) {
                $requestOptions['facetFilters'][] = array_map(
                    function ($term) use ($queryFacet) {
                        return $queryFacet->handle . ':' . $term;
                    },
                    $queryFacet->terms
                );
            } elseif ($queryFacet instanceof RangeFacet) {
                $requestOptions['numericFilters'][] = sprintf(
                    '%s: %s to %s',
                    $queryFacet->handle,
                    $queryFacet->min,
                    $queryFacet->max
                );
            }
        }

        if ($query->productId) {
            $requestOptions['facetFilters'][] = 'productId:' . $query->productId;
        }

        if ($query->productIds) {
            foreach ($query->productIds as $productId) {
                $requestOptions['facetFilters'][] = 'productId:' . $productId;
            }
        }

        if ($query->sku) {
            $requestOptions['facetFilters'][] = 'sku:' . $query->sku;
        }

        if ($query->skus) {
            foreach ($query->skus as $sku) {
                $requestOptions['facetFilters'][] = 'sku:' . $sku;
            }
        }

        if ($query->productType) {
            $requestOptions['facetFilters'][] = 'productType:' . $query->productType;
        }

        if ($query->category) {
            $requestOptions['facetFilters'][] = 'category:' . $query->category;
        }

        return Create::promiseFor(
            $this->client->search(
                $queryTerm,
                array_merge($query->rawApiInput, $requestOptions)
            )
        )
        ->then(function ($response) use ($query) {
            $totalResults = $response['nbHits'];

            $items = [];
            foreach ($response['hits'] as $hit) {
                // When the `distinct` request option is enabled, the response does not contain duplicated
                // products per each variant.
                $items[] = new Product([
                    'productId' => $hit['productId'] ?? null,
                    'name' => $hit['name'] ?? null,
                    'slug' => $hit['slug'] ?? null,
                    'description' => $hit['description'] ?? null,
                    'categories' => $hit['categories'] ?? [],
                    'variants' => [
                        // Algolia always return a single variant per hit.
                        new Variant([
                            'id' => $hit['productId'] ?? null,
                            'sku' => $hit['sku'] ?? null,
                            'price' => intval($hit['price'] * 100),
                            'attributes' => $hit, // TODO: should we remove already mapped values?
                            'images' => $hit['images'] ?? [],
                            'dangerousInnerVariant' => $query->loadDangerousInnerData ? $hit : null
                        ])
                    ],
                    'dangerousInnerProduct' => $query->loadDangerousInnerData ? $hit : null
                ]);
            }

            return new Result(
                [
                    'offset' => $response['offset'] ?? 0,
                    'total' => $totalResults,
                    'items' => $items,
                    'count' => count($items),
                    'facets' => $this->dataToFacets($response),
                    'query' => clone $query,
                ]
            );
        });
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        // Only "TermFacet" and "price" will be returned as a searchable attributes. We are ignoring
        // Attribute::TYPE_TEXT as a searchable attributes since Algolia does not allow filter
        // those attributes along with a query filter, and also the text searched needs to be an exact match.
        // In this way, we are prioritizing query filter that will allow us to perform partial searches.

        return Create::promiseFor(
            $this->client->getSettings()
        )
        ->then(function ($response) {
            $attributes = [];
            foreach ($response['searchableAttributes'] as $searchableAttributeData) {
                $searchableAttributeKey = preg_replace(
                    '/unordered\((.*?)\)/',
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

            $searchResponse = $this->client->search(
                '',
                [
                    'attributesToRetrieve' => ['objectID'], // don't retrieve full objects
                    'hitsPerPage' => 0, // send back an empty page of results anyway
                    'facets' => '*', // ask for all facets
                    'responseFields' => ['facets', 'facets_stats'], // limit JSON response fields
                ]
            );

            $facets = $this->dataToFacets($searchResponse);
            foreach ($facets as $facet) {
                if ($facet instanceof Result\TermFacet) {
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
                if ($facet instanceof Result\RangeFacet && $facet->key == 'price') {
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
        });
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @param array $data
     * @return ResultFacet[]
     */
    protected function dataToFacets(array $data): array
    {
        $facets = [];

        $facetsData = $data['facets'] ?? [];
        foreach ($facetsData as $facetKey => $facetTerms) {
            if ($this->shouldIgnoreAttributeKey($facetKey)) {
                continue;
            }

            $terms = [];
            foreach ($facetTerms as $term => $count) {
                $terms[] = new Term([
                    'handle' => $term,
                    'name' => $term,
                    'value' => $term,
                    'count' => $count,
                    // TODO: implement `selected`
                ]);
            }

            $facets[] = new Result\TermFacet([
                'handle' => $facetKey,
                'key' => $facetKey,
                'terms' => $terms,
                // TODO: implement `selected`
            ]);
        }

        $facetsStatsData = $data['facets_stats'] ?? [];
        foreach ($facetsStatsData as $facetKey => $facetStat) {
            if ($this->shouldIgnoreAttributeKey($facetKey)) {
                continue;
            }

            $facets[] = new Result\RangeFacet([
                'handle' => $facetKey,
                'key' => $facetKey,
                'min' => $facetStat['min'] ?? null,
                'max' => $facetStat['max'] ?? null,
            ]);
        }

        return $facets;
    }

    protected function shouldIgnoreAttributeKey(string $attributeKey): bool
    {
        return in_array($attributeKey, self::IGNORED_ATTRIBUTES);
    }

    protected function extractRangeValues(RangeFilter $filter): array
    {
        $min = $filter->min;
        $max = $filter->max;

        if ($filter->attributeType == Attribute::TYPE_MONEY) {
            $min = $min / 100;
            $max = $max / 100;
        }

        return [$min, $max];
    }
}
