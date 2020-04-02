<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class SapClientFactory
{
    /** @var HttpClient */
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function factorForProjectAndType(Project $project, string $typeName): SapClient
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $sapConfig = $project->getConfigurationSection('sap-commerce-cloud');

        $config = [];
        foreach (['hostUrl', 'siteId', 'catalogId', 'catalogVersionId'] as $option) {
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
            $config['hostUrl'],
            $config['siteId'],
            $config['catalogId'],
            $config['catalogVersionId']
        );
    }
}
