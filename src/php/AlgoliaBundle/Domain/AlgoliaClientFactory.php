<?php

namespace Frontastic\Common\AlgoliaBundle\Domain;

class AlgoliaClientFactory
{
    public function factorForConfigs(
        array $languages,
        object $typeSpecificConfig,
        ?object $algoliaConfig = null
    ): AlgoliaClient {
        $config = [];
        foreach (['appId', 'appKey', 'indexName'] as $option) {
            $value = $typeSpecificConfig->$option ?? $algoliaConfig->$option ?? null;

            if ($value === null) {
                throw new \RuntimeException('Algolia config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('Algolia config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('Algolia config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        return new AlgoliaClient(
            $config['appId'],
            $config['appKey'],
            $config['indexName'],
        );
    }

}
