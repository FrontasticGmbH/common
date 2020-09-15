<?php

namespace Frontastic\Common\FindologicBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\SimpleCache\CacheInterface;

class FindologicClientFactory
{
    /** @var HttpClient */
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function factorForConfigs(
        array $languages,
        object $typeSpecificConfig,
        ?object $findologicConfig = null
    ): FindologicClient {
        $clientConfigs = [];

        $languagesConfig = $typeSpecificConfig->languages ?? $findologicConfig->languages ?? null;

        foreach ($languages as $language) {
            $languageConfig = (object) ($languagesConfig[$language] ?? null);
            $config = [];

            foreach (['hostUrl', 'shopkey'] as $option) {
                $value = $languageConfig->$option ?? $typeSpecificConfig->$option ?? $findologicConfig->$option ?? null;

                if ($value === null) {
                    throw new \RuntimeException(
                        'Findologic config option ' . $option . ' is not set for language' . $language
                    );
                }
                if (!is_string($value)) {
                    throw new \RuntimeException(
                        'Findologic config option ' . $option . ' is no string for language ' . $language
                    );
                }
                if ($value === '') {
                    throw new \RuntimeException(
                        'Findologic config option ' . $option . ' is empty for language' . $language
                    );
                }

                $config[$option] = $value;
            }

            $clientConfigs[$language] = new FindologicEndpointConfig($config);
        }

        $outputAttributes = $typeSpecificConfig->outputAttributes ?? $findologicConfig->outputAttributes ?? [];

        if (!is_array($outputAttributes)) {
            throw new \RuntimeException('Findologic config option outputAttributes needs to be an array');
        }

        return new FindologicClient($this->httpClient, $clientConfigs, $outputAttributes);
    }
}
