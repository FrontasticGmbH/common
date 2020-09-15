<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Domain\SearchRequest;
use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\FindologicBundle\Exception\UnsupportedQueryException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;
use function GuzzleHttp\Promise\all;

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

    /**
     * @var string[]
     */
    private $languages;

    public function __construct(
        FindologicClient $client,
        ProductSearchApi $originalDataSource,
        Mapper $mapper,
        QueryValidator $validator,
        LoggerInterface $logger,
        array $languages
    ) {
        $this->client = $client;
        $this->originalDataSource = $originalDataSource;
        $this->mapper = $mapper;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->languages = $languages;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        /**
         * Fall back to original data source for SKU-based searches. Findologic does not support this in a reliable
         * way out of the box since it always executes a full text search across all fields when using "query".
         */
        if ($query->sku !== null  || !empty($query->skus)) {
            $this->logger->info(
                'ProductSearchApi: Falling back to original data source for searching by SKU.'
            );
            return $this->originalDataSource->query($query);
        }

        $validationResult = $this->validator->isSupported($query);

        if (!$validationResult->isSupported) {
            throw new UnsupportedQueryException($validationResult->validationError);
        }

        $request = $this->mapper->queryToRequest($query);

        return $this->client->search($query->locale, $request)
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

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        return $this->originalDataSource->getSearchableAttributes()
            ->then(function (array $originalAttributes) {
                $attributesRequest = new SearchRequest(['count' => 1]);

                $attributeRequests = array_map(
                    function ($language) use ($attributesRequest) {
                        return $this->client->search($language, $attributesRequest)
                            ->then(function ($result) {
                                $availableAttributeData = array_merge(
                                    $result['body']['result']['filters']['main'],
                                    $result['body']['result']['filters']['other']
                                );

                                return array_map(
                                    function (array $attributeData) {
                                        return $attributeData['name'];
                                    },
                                    $availableAttributeData
                                );
                            });
                    },
                    $this->languages
                );

                return all($attributeRequests)
                    ->then(function (array $attributeIds) use ($originalAttributes) {
                        // Only use attributes available across all locales
                        $availableAttributeIds = array_intersect(...$attributeIds);

                        return array_filter(
                            $originalAttributes,
                            function (Attribute $originalAttribute) use ($availableAttributeIds) {
                                return in_array($originalAttribute->attributeId, $availableAttributeIds);
                            }
                        );
                    });
            });
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
