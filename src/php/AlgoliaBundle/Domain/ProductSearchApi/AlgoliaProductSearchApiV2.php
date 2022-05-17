<?php

namespace Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi;

use Frontastic\Common\AlgoliaBundle\Domain\AlgoliaClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;

class AlgoliaProductSearchApiV2 extends ProductSearchApiBase
{
    /**
     * @var AlgoliaClient
     */
    private $client;

    /**
     * @var EnabledFacetService
     */
    private $enabledFacetService;

    /**
     * @var MapperV2
     */
    private $mapper;

    public function __construct(
        AlgoliaClient $client,
        EnabledFacetService $enabledFacetService,
        MapperV2 $mapper
    ) {
        $this->client = $client;
        $this->enabledFacetService = $enabledFacetService;
        $this->mapper = $mapper;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        // The index selected should have configured `productId` as "Attribute for Distinct"
        // https://www.algolia.com/doc/guides/managing-results/refine-results/grouping/how-to/item-variations

        // In order to perform filter by `productIds`, `skus`, `productType` or `category` the Algolia index should
        // have those fields set as facets.

        return Create::promiseFor(
            $this->client
                ->setLanguage($query->locale)
                ->setSortIndex($query->sortAttributes)
                ->search(
                    $query->query ?? '',
                    array_merge($query->rawApiInput, $this->getRequestOptions($query))
                )
        )
        ->then(function ($response) use ($query) {
            $totalResults = $response['nbHits'];

            $products = $this->mapper->dataToProducts($response['hits'], $query);

            return new Result(
                [
                    'offset' => $response['offset'] ?? 0,
                    'total' => $totalResults,
                    'items' => $products,
                    'count' => count($products),
                    'facets' => $this->mapper->dataToFacets($response, $query),
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
        ->then(function ($settingsResponse) {
            $searchResponse = $this->client->search(
                '',
                [
                    'attributesToRetrieve' => ['objectID'], // Don't retrieve full objects
                    'hitsPerPage' => 0, // Send back an empty page of results anyway
                    'facets' => '*', // Ask for all facets
                    'responseFields' => ['facets', 'facets_stats'], // Limit JSON response fields
                ]
            );

            return $this->mapper->dataToAttributes($settingsResponse, $searchResponse);
        });
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    protected function getRequestOptions(ProductQuery $query): array
    {
        $requestOptions = [
            'distinct' => true, // Enable the "Attribute for Distinct" to ensure that products are not duplicated.
            'length' => $query->limit,
            'offset' => $query->offset,
        ];

        $filters = [];
        foreach ($query->filter as $queryFilter) {
            if ($queryFilter instanceof TermFilter) {
                $terms = array_map(
                    function ($term) use ($queryFilter) {
                        return $queryFilter->handle . ":'$term'";
                    },
                    $queryFilter->terms
                );
                if (!empty($terms)) {
                    $filters[] = '(' . implode(' OR ', $terms) . ')';
                }
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

        return $requestOptions;
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
