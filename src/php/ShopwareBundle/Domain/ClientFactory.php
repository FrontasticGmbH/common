<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use RuntimeException;

class ClientFactory
{
    private const MAIN_CONFIGURATION_SECTION = 'shopware';

    /**
     * @var string[]
     */
    private $requiredConfigOptions = [
        'apiKey',
        'endpoint',
        'clientId',
        'clientSecret',
    ];

    /**
     * @var \Frontastic\Common\HttpClient
     */
    private $httpClient;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(HttpClient $httpClient, Cache $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function factorForConfigs(object $typeSpecificConfiguration, ?object $genericConfiguration = null): Client
    {
        $config = $this->resolveConfiguration($typeSpecificConfiguration, $genericConfiguration);

        return new Client(
            $this->httpClient,
            $this->cache,
            $config['apiKey'],
            $config['endpoint'],
            $config['clientId'],
            $config['clientSecret'],
            property_exists($genericConfiguration, 'apiVersion') ?
                (string)$genericConfiguration->apiVersion :
                '6.3',
        );
    }

    public function factorForProjectAndType(Project $project, string $typeName): Client
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $genericConfiguration = $project->getConfigurationSection(self::MAIN_CONFIGURATION_SECTION);

        return $this->factorForConfigs($typeSpecificConfiguration, $genericConfiguration);
    }

    private function resolveConfiguration(object $typeSpecificConfiguration, object $genericConfiguration): array
    {
        $resolved = [];
        foreach ($this->requiredConfigOptions as $option) {
            $value = $typeSpecificConfiguration->$option ?? $genericConfiguration->$option ?? null;

            $this->assertConfigurationValue($option, $value);

            $resolved[$option] = $value;
        }

        return $resolved;
    }

    /**
     * @param string $option
     * @param mixed $value
     *
     * @return void
     */
    private function assertConfigurationValue(string $option, $value): void
    {
        if ($value === null) {
            throw new RuntimeException(
                sprintf('Failed to create client. Shopware config option `%s` is not set', $option)
            );
        }

        if (!is_string($value)) {
            throw new RuntimeException(
                sprintf('Failed to create client. Shopware config option `%s` is not a string', $option)
            );
        }

        if ($value === '') {
            throw new RuntimeException(
                sprintf('Failed to create client. Shopware config option `%s` is empty', $option)
            );
        }
    }
}
