<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\FindologicBundle\Exception\UnsupportedQueryException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

class FindologicProductSearchApi extends ProductSearchApiBase
{
    /**
     * @var FindologicClient
     */
    private $client;

    /**
     * @var ProductSearchApi
     */
    private $originalDataSource;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var QueryValidator
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FindologicClient $client,
        ProductSearchApi $originalDataSource,
        Mapper $mapper,
        QueryValidator $validator,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->originalDataSource = $originalDataSource;
        $this->mapper = $mapper;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
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
                        $this->logger->info(
                            'ProductSearchApi: Findologic service unavailable - falling back to original data source.'
                        );
                        return $this->originalDataSource->query($query);
                    }

                    throw $reason;
                }
            );
    }

    protected function getSearchableAttributesImplementation(): array
    {
        // TODO: Implement getSearchableAttributes() method.
        return [];
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
