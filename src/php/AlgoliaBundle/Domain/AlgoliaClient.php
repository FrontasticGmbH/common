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

    protected function initIndex(AlgoliaIndexConfig $indexConfig, ?string $indexOverride = null)
    {
        $client = SearchClient::create($indexConfig->appId, $indexConfig->appKey);

        $this->index = $client->initIndex($indexOverride ?? $indexConfig->indexName);
    }

    protected function getSortIndex(array $sortAttributes): ?string
    {
        if (count($sortAttributes) < 1) {
            return null;
        }

        $sortIndices = $this->defaultIndexConfig->sortIndices;

        foreach ($sortIndices as $key => $sortIndex) {
            $match = true;
            $length = 0;

            // Order is not guaranteed so we have to check each attribute individually
            // We also have to check the length of the key to only allow full matches
            foreach ($sortAttributes as $name => $mode) {
                $length += strlen("{$name}_{$mode}");
                if (strpos($key, "{$name}_{$mode}") === false) {
                    $match = false;
                    break;
                }
            }

            // Attributes are delimited with an underscore, we need to count those as well
            // e.g price_asc_rating_desc -> 1 extra underscore
            $attributeDelimiterCount = count($sortAttributes) - 1;

            if ($match && strlen($key) === $length + $attributeDelimiterCount && is_string($sortIndex)) {
                return $sortIndex;
            }
        }

        return null;
    }

    public function setLanguage(string $language): self
    {
        $indexConfig = $this->indexesConfig[$language] ?? $this->defaultIndexConfig;
        if ($this->index->getIndexName() != $indexConfig->indexName) {
            $this->initIndex($indexConfig);
        }

        return $this;
    }

    public function setSortIndex(array $sortAttributes): self
    {
        $sortIndex = $this->getSortIndex($sortAttributes);

        if ($sortIndex !== null) {
            $this->initIndex($this->defaultIndexConfig, $sortIndex);
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
