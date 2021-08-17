<?php

namespace Frontastic\Common\AlgoliaBundle\Domain;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

class AlgoliaClient
{
    /**
     * @var SearchIndex
     */
    private $index;

    public function __construct(string $appId, string $appKey, string $indexName)
    {
        $this->index = SearchClient::create($appId, $appKey)
            ->initIndex($indexName);
    }

    public function search(): PromiseInterface
    {
        $objects = $this->index->search('', [
                'distinct' => true,
            ]
        );

        return new Promise();
    }

}
