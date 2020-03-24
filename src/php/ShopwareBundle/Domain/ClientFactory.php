<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use RuntimeException;

class ClientFactory
{
    private const MAIN_CONFIGURATION_SECTION = 'shopware';

    private $requiredConfigOptions = [
        'apiKey',
        'endpoint',
    ];

    /**
     * @var \Frontastic\Common\HttpClient
     */
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function factor(Project $project): Client
    {
        $configuration = $project->getConfigurationSection(self::MAIN_CONFIGURATION_SECTION);

        $this->assertConfiguration($configuration);

        return new Client(
            $this->httpClient,
            $configuration->apiKey,
            $configuration->endpoint
        );
    }

    private function assertConfiguration(object $configuration): void
    {
        foreach ($this->requiredConfigOptions as $option) {
            if (!isset($configuration->$option) || empty($configuration->$option)) {
                throw new RuntimeException(
                    sprintf(
                        'Failed to create client. Required config option `%s` for engine `shopware` is not defined or is empty',
                        $option
                    )
                );
            }
        }
    }
}
