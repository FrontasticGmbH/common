<?php

namespace Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi;

use Frontastic\Common\AlgoliaBundle\Domain\AlgoliaClient;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;

class AlgoliaProductSearchApi extends ProductSearchApiBase
{
    /**
     * @var AlgoliaClient
     */
    private $client;

    public function __construct(AlgoliaClient $client)
    {
        $this->client = $client;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $queryTerm = $query->query ?? '';

        $requestOptions = [
            'distinct' => true, // Enable the "Attribute for Distinct" to ensure that products are not duplicated.
            'length' => $query->limit,
            'offset' => $query->offset,
            'facets' => '*',
            // TODO: use cursor instead of offset
            // 'hitsPerPage' => $query->limit,
            // 'page' => (int)ceil($query->cursor / $query->limit),
        ];

        foreach ($query->filter as $queryFilter) {
            if ($queryFilter instanceof TermFilter) {
                $requestOptions['facetFilters'][] = array_map(
                    function ($term) use ($queryFilter) {
                        // Format expected "<filter_name>:<filter_value>"
                        return $queryFilter->handle . ':' . $term;
                    },
                    $queryFilter->terms
                );
            }
        }

        // TODO: implement $query->productIds
        // TODO: implement $query->skus
        // TODO: implement $query->productType
        // TODO: implement $query->category
        // TODO: implement $query->sortAttributes
        // TODO: implement $query->facets

        // The index selected should have configured `productId` as "Attribute for Distinct"
        // https://www.algolia.com/doc/guides/managing-results/refine-results/grouping/how-to/item-variations

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
                // products per each variant
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

                $attributes[$searchableAttributeKey] =  new Attribute([
                    'attributeId' => $searchableAttributeKey,
                    'type' => Attribute::TYPE_TEXT, // Use text type as default
                ]);
            }

            $searchResponse = $this->client->search(
                '',
                [
                    'attributesToRetrieve' => ['objectID'], // don't retrieve full objects
                    'hitsPerPage' => 0, // send back an empty page of results anyway
                    'facets' => '*', // ask for all facets,
                    'responseFields' => 'facets', // limit JSON response to `facets`
                ]
            );

            $facets = $this->dataToFacets($searchResponse);
            foreach ($facets as $facet) {
                if (!key_exists($facet->key, $attributes)) {
                    continue;
                }

                if ($facet instanceof Result\TermFacet) {
                    $attributes[$facet->key]->type = Attribute::TYPE_ENUM;
                    $attributes[$facet->key]->values = array_map(
                        function ($term) {
                            return [
                                'key' => $term->value,
                                'label' => $term->value,
                            ];
                        },
                        $facet->terms
                    );
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
     * @return Facet[]
     */
    protected function dataToFacets(array $data): array
    {
        $facets = [];

        $facetsData = $data['facets'] ?? [];
        foreach ($facetsData as $facetKey => $facetTerms) {
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
            $facets[] = new Result\RangeFacet([
                'handle' => $facetKey,
                'key' => $facetKey,
                'min' => $facetStat['min'] ?? null,
                'max' => $facetStat['max'] ?? null,
            ]);
        }

        return $facets;
    }
}
