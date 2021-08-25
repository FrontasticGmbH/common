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

    /**
     * @var array
     */
    private $indexesConfig;

    /**
     * @var AlgoliaIndexConfig
     */
    private $defaultIndexConfig;

    /**
     * @param array<string, AlgoliaIndexConfig> $indexesConfig
     */
    public function __construct(array $indexesConfig)
    {
        $this->indexesConfig = $indexesConfig;
        $this->defaultIndexConfig = current($indexesConfig); // Use the first index config as default

        $this->initIndex($this->defaultIndexConfig);
    }

    protected function initIndex(AlgoliaIndexConfig $indexConfig)
    {
        $this->index = SearchClient::create($indexConfig->appId, $indexConfig->appKey)
            ->initIndex($indexConfig->indexName);
    }

    public function search(string $query, array $requestOptions = [])
    {
        return $this->index->search($query, $requestOptions);
    }

    public function getSettings()
    {
        return $this->index->getSettings();
    }
}
