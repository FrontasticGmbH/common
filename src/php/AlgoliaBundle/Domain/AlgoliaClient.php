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
    public function __construct(array $indexesConfig, string $defaultLanguage)
    {
        $this->indexesConfig = $indexesConfig;
        $this->defaultIndexConfig = $indexesConfig[$defaultLanguage] ?? current($indexesConfig);

        $this->initIndex($this->defaultIndexConfig);
    }

    protected function initIndex(AlgoliaIndexConfig $indexConfig)
    {
        $this->index = SearchClient::create($indexConfig->appId, $indexConfig->appKey)
            ->initIndex($indexConfig->indexName);
    }

    public function setLanguage(string $language): self
    {
        $indexConfig = $this->indexesConfig[$language] ?? $this->defaultIndexConfig;
        if ($this->index->getIndexName() != $indexConfig->indexName) {
            $this->initIndex($indexConfig);
        }

        return $this;
    }

    public function search(string $query, array $requestOptions)
    {
        return $this->index->search($query, $requestOptions);
    }

    public function getSettings()
    {
        return $this->index->getSettings();
    }
}
