<?php
namespace Frontastic\Common\FindologicBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\RequestProvider;
use Frontastic\Common\HttpClient;

class FindologicClientFactory
{
    /** @var HttpClient */
    private $httpClient;

    /** @var RequestProvider */
    private $requestProvider;

    public function __construct(HttpClient $httpClient, RequestProvider $requestProvider)
    {
        $this->httpClient = $httpClient;
        $this->requestProvider = $requestProvider;
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
                        'Findologic config option ' . $option . ' is not set for language ' . $language
                    );
                }
                if (!is_string($value)) {
                    throw new \RuntimeException(
                        'Findologic config option ' . $option . ' is no string for language ' . $language
                    );
                }
                if ($value === '') {
                    throw new \RuntimeException(
                        'Findologic config option ' . $option . ' is empty for language ' . $language
                    );
                }

                $config[$option] = $value;
            }

            $clientConfigs[$language] = new FindologicEndpointConfig($config);
        }

        return new FindologicClient($this->httpClient, $this->requestProvider, $clientConfigs);
    }
}
