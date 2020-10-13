<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\SimpleCache\CacheInterface;

class SapClientFactory
{
    /** @var HttpClient */
    private $httpClient;

    /** @var CacheInterface */
    private $cache;

    public function __construct(HttpClient $httpClient, CacheInterface $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function factorForConfigs(object $typeSpecificConfiguration, ?object $sapConfig = null): SapClient
    {
        $config = [];
        foreach (['hostUrl', 'siteId', 'clientId', 'clientSecret', 'catalogId', 'catalogVersionId'] as $option) {
            $value = $typeSpecificConfiguration->$option ?? $sapConfig->$option ?? null;

            if ($value === null) {
                throw new \RuntimeException('SAP Commerce Cloud config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('SAP Commerce Cloud config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('SAP Commerce Cloud config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        return new SapClient(
            $this->httpClient,
            $this->cache,
            $config['hostUrl'],
            $config['siteId'],
            $config['clientId'],
            $config['clientSecret'],
            $config['catalogId'],
            $config['catalogVersionId']
        );
    }

    public function factorForProjectAndType(Project $project, string $typeName): SapClient
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $sapConfig = $project->getConfigurationSection('sap-commerce-cloud');

        return $this->factorForConfigs($typeSpecificConfiguration, $sapConfig);
    }
}
