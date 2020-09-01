<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\FindologicBundle\Exception\UnsupportedQueryException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
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

    /**
     * @var QueryValidator
     */
    private $validator;

    public function __construct(
        FindologicClient $client,
        ProductSearchApi $fallback,
        Mapper $mapper,
        QueryValidator $validator
    ) {
        $this->client = $client;
        $this->fallback = $fallback;
        $this->mapper = $mapper;
        $this->validator = $validator;
    }

    public function query(ProductQuery $query): PromiseInterface
    {
        $validationResult = $this->validator->isSupported($query);

        if (!$validationResult->isSupported) {
            throw new UnsupportedQueryException($validationResult->validationError);
        }

        $request = $this->mapper->queryToRequest($query);

        return $this->client->search($request)
            ->then(
                function ($result) use ($query) {
                    $currentCursor = $query->cursor ?? $query->offset ?? null;
                    $previousCursor = $currentCursor - $query->limit;

                    return new Result(
                        [
                            'offset' => $result['body']['request']['first'],
                            'total' => $result['body']['result']['metadata']['totalResults'],
                            'previousCursor' => $previousCursor < 0 ? null : $previousCursor,
                            'nextCursor' => ($currentCursor) + $query->limit,
                            'count' => count($result['body']['result']['items']),
                            'items' => $this->mapper->dataToProducts($result['body']['result']['items'], $query),
                            'facets' => $this->mapper->dataToFacets($result['body']['result']['filters'], $query),
                            'query' => clone $query,
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
        return [];
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
