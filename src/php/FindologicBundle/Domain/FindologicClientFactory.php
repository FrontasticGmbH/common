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

    public function factorForProjectAndType(Project $project, string $typeName): FindologicClient
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $findologicConfig = $project->getConfigurationSection('findologic');

        $config = [];
        foreach (['hostUrl', 'storefrontAccessToken'] as $option) {
            $value = $typeSpecificConfiguration->$option ?? $findologicConfig->$option ?? null;

            if ($value === null) {
                throw new \RuntimeException('Shopify config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('Shopify config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('Shopify config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        return new FindologicClient(
            $this->httpClient,
            $config['hostUrl'],
            $config['shopkey']
        );
    }
}
