<?php

namespace Frontastic\Common\SprykerBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\Domain\Exception\ExceptionFactoryInterface;
use RuntimeException;

class SprykerClientFactory
{
    private const MAIN_CONFIGURATION_SECTION = 'spryker';

    /**
     * @var string[]
     */
    private $requiredConfigOptions = [
        'endpoint',
    ];

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var ExceptionFactoryInterface
     */
    private $exceptionFactory;

    public function __construct(HttpClient $httpClient, ExceptionFactoryInterface $exceptionFactory)
    {
        $this->httpClient = $httpClient;
        $this->exceptionFactory = $exceptionFactory;
    }

    public function factorForProjectAndType(Project $project, string $typeName): SprykerClient
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $genericConfiguration = $project->getConfigurationSection(self::MAIN_CONFIGURATION_SECTION);

        $config = $this->resolveConfiguration($typeSpecificConfiguration, $genericConfiguration);

        return new SprykerClient(
            $this->httpClient,
            $config['endpoint'],
            $this->exceptionFactory
        );
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
                sprintf('Failed to create client. Spryker config option `%s` is not set', $option)
            );
        }

        if (!is_string($value)) {
            throw new RuntimeException(
                sprintf('Failed to create client. Spryker config option `%s` is not a string', $option)
            );
        }

        if ($value === '') {
            throw new RuntimeException(
                sprintf('Failed to create client. Spryker config option `%s` is empty', $option)
            );
        }
    }
}
