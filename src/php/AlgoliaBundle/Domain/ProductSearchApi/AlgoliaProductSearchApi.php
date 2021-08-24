<?php

namespace Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi;

use Frontastic\Common\AlgoliaBundle\Domain\AlgoliaClient;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet;
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
                        // Algolia always returns a single variant per hit.
                        new Variant([
                            'id' => $hit['productId'] ?? null,
                            'sku' => $hit['sku'] ?? null,
                            'price' => intval($hit['price'] * 100),
                            'attributes' => $hit, // Store all attributes returned by Algolia.
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
                    'facets' => $this->dataToFacets($response, $query),
                    'query' => clone $query,
                ]
            );
        });
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
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
                    'attributesToRetrieve' => ['objectID'], // Don't retrieve full objects
                    'hitsPerPage' => 0, // Send back an empty page of results anyway
                    'facets' => '*', // Ask for all facets
                    'responseFields' => ['facets', 'facets_stats'], // Limit JSON response fields
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
     * @param ProductQuery|null $query
     *
     * @return ResultFacet[]
     */
    protected function dataToFacets(array $data, ProductQuery $query = null): array
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
                $terms[] = new Term([
                    'handle' => $term,
                    'name' => $term,
                    'value' => $term,
                    'count' => $count,
                    'selected' => isset($selectedTermsMap[$term]),
                ]);
            }

            $facets[] = new Result\TermFacet([
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

            $facets[] = new Result\RangeFacet($facetValues);
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

    protected function findFacetQuery(ProductQuery $query, string $facetKey): ?Facet
    {
        foreach ($query->facets as $facetQuery) {
            if ($facetQuery->handle === $facetKey) {
                return $facetQuery;
            }
        }
        return null;
    }
}
