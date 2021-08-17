<?php

namespace Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi;

use Frontastic\Common\AlgoliaBundle\Domain\AlgoliaClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
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
        return $this->client->search();
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        return new Promise();
    }

    public function getDangerousInnerClient()
    {
        // TODO: Implement getDangerousInnerClient() method.
    }
}
