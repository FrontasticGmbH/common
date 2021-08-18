<?php

namespace Frontastic\Common\AlgoliaBundle\Domain;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;

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

    public function search(string $query, array $requestOptions = [])
    {
        return $this->index->search($query, $requestOptions);
    }
}
