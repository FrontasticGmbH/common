<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Contentful\Delivery\ClientOptions;
use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class GraphCMSClientFactory
{
    private HttpClient $httpClient;
    private Cache $cache;

    public function __construct(HttpClient $httpClient, Cache $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function factorForConfigs(
        object $typeSpecificConfiguration,
        ?object $graphcmsConfig = null
    ): Client {
        $config = [];

        foreach ($this->getStringConfigOptions() as $option => $defaultValue) {
            $value = $typeSpecificConfiguration->$option ?? $graphcmsConfig->$option ?? $defaultValue;
            if ($value === null) {
                throw new \RuntimeException('GraphCMS config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('GraphCMS config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('GraphCMS config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        return new Client(
            $config['projectId'],
            $config['apiToken'],
            $config['apiVersion'],
            $config['region'],
            $config['stage'],
            $this->httpClient,
            $this->cache
        );
    }

    public function factorForProjectAndType(Project $project, string $typeName): Client
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $graphcmsConfig = $project->getConfigurationSection('graphcms');

        return $this->factorForConfigs($typeSpecificConfiguration, $graphcmsConfig);
    }

    /**
     * @return array<string, string|null>
     */
    private function getStringConfigOptions(): array
    {
        return [
            'projectId' => null,
            'apiToken' => null,
            'apiVersion' => 'v1',
            'region' => null,
            'stage' => null,
        ];
    }
}
