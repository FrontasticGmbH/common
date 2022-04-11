<?php

namespace Frontastic\Common\AlgoliaBundle\Domain;

class AlgoliaClientFactory
{
    public function factorForConfigs(
        array $languages,
        string $defaultLanguage,
        object $typeSpecificConfig,
        ?object $algoliaConfig = null
    ): AlgoliaClient {
        $clientConfigs = [];

        $languagesConfig = $typeSpecificConfig->languages ?? $algoliaConfig->languages ?? null;
        foreach ($languages as $language) {
            $languageConfig = (object) ($languagesConfig[$language] ?? null);
            $config = [];

            foreach (['appId', 'appKey', 'indexName'] as $option) {
                $value = $languageConfig->$option ?? $typeSpecificConfig->$option ?? $algoliaConfig->$option ?? null;

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

            $config['sortIndices'] = $languageConfig->sortIndices
                ?? $typeSpecificConfig->sortIndices
                ?? $algoliaConfig->sortIndices
                ?? [];

            $clientConfigs[$language] = new AlgoliaIndexConfig($config);
        }

        return new AlgoliaClient($clientConfigs, $defaultLanguage);
    }
}
