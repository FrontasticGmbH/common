<?php

namespace Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi;

use Frontastic\Common\AlgoliaBundle\Domain\AlgoliaClient;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\Promise;
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

        // The index selected should have configured `productId` as "Attribute for Distinct"
        // https://www.algolia.com/doc/guides/managing-results/refine-results/grouping/how-to/item-variations

        $requestOptions = [
            'distinct' => true, // Enable the "Attribute for Distinct" to ensure that products are not duplicated.
            'length' => $query->limit,
            'offset' => $query->offset,
            // TODO: use cursor instead of offset
            // 'hitsPerPage' => $query->limit,
            // 'page' => (int)ceil($query->cursor / $query->limit),
        ];

        return Create::promiseFor(
            $this->client->search(
                $queryTerm,
                array_merge($query->rawApiInput, $requestOptions)
            )
        )
        ->then(function ($result) use ($query) {
            $totalResults = $result['nbHits'];

            $items = [];
            foreach ($result['hits'] as $hit) {
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
                    'offset' => $result['offset'] ?? 0,
                    'total' => $totalResults,
                    'query' => clone $query,
                    'items' => $items,
                    'count' => count($items),
                ]
            );
        });
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        return new Promise();
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
