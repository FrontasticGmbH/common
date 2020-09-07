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

    public function factorForConfigs(object $typeSpecificConfig, ?object $defaultConfig = null): FindologicClient
    {
        $config = [];
        foreach (['hostUrl', 'shopkey'] as $option) {
            $value = $typeSpecificConfig->$option ?? $defaultConfig->$option ?? null;

            if ($value === null) {
                throw new \RuntimeException('Findologic config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('Findologic config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('Findologic config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        return new FindologicClient($this->httpClient, $config['hostUrl'], $config['shopkey']);
    }

    public function factorForProjectAndType(Project $project, string $typeName): FindologicClient
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $findologicConfig = $project->getConfigurationSection('findologic');

        return $this->factorForConfigs($typeSpecificConfiguration, $findologicConfig);
    }
}
