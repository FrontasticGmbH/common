<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Domain\SearchRequest;
use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Filter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use GuzzleHttp\Promise\PromiseInterface;

class FindologicProductSearchApi implements ProductSearchApi
{
    /**
     * @var FindologicClient
     */
    private $client;

    /**
     * @var ProductSearchApi
     */
    private $fallback;

    /**
     * @var Mapper
     */
    private $mapper;

    public function __construct(FindologicClient $client, ProductSearchApi $fallback, Mapper $mapper)
    {
        $this->client = $client;
        $this->fallback = $fallback;
        $this->mapper = $mapper;
    }

    public function query(ProductQuery $query): PromiseInterface
    {
        $currentCursor = $query->cursor ?? $query->offset ?? null;

        $request = $this->buildSearchRequest($query, $currentCursor);

        return $this->client->search($request)
            ->then(
                function ($result) use ($query, $currentCursor) {
                    $previousCursor = $currentCursor - $query->limit;

                    return new Result(
                        [
                            'query' => clone $query,
                            'offset' => $result['body']['request']['first'],
                            'count' => count($result['body']['result']['items']),
                            'total' => $result['body']['result']['metadata']['totalResults'],
                            'items' => $this->mapper->dataToProducts($result['body']['result']['items'], $query),
                            'previousCursor' => $previousCursor < 0 ? null : $previousCursor,
                            'nextCursor' => ($currentCursor) + $query->limit,
                        ]
                    );
                }
            )
            ->otherwise(
                function ($reason) use ($query) {
                    if ($reason instanceof ServiceNotAliveException) {
                        // @TODO log fallback usage
                        return $this->fallback->query($query);
                    }

                    throw $reason;
                }
            );
    }

    public function getSearchableAttributes(): array
    {
        // TODO: Implement getSearchableAttributes() method.
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }


    /**
     * @param ProductQuery $query
     * @param int|null $currentCursor
     * @return SearchRequest
     */
    private function buildSearchRequest(ProductQuery $query, ?int $currentCursor): SearchRequest
    {
        $parameters = [
            'query' => $query->query,
            'first' => $currentCursor ?? $query->offset ?? null,
            'count' => $query->limit,
            'order' => $this->mapper->sortAttributesToRequest($query),
            'attributes' => $this->mapper->attributesToRequest($query)
        ];

        return new SearchRequest($parameters);
    }
}
