<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

/**
 * This factory can be used to create instances of the Commercetools Client class. From within the Catwalk, you might
 * as well use the `\Frontastic\Catwalk\ApiCoreBundle\Domain\CommerceTools\ClientFactory`.
 */
class ClientFactory
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(Cache $cache, HttpClient $httpClient)
    {
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    public function factorForConfigs(object $typeSpecificConfiguration, ?object $commercetoolsConfig = null): Client
    {
        $config = [];

        foreach ($this->getStringConfigOptions() as $option => $defaultValue) {
            $value = $typeSpecificConfiguration->$option ?? $commercetoolsConfig->$option ?? $defaultValue;
            if ($value === null) {
                throw new \RuntimeException('Commercetools config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('Commercetools config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('Commercetools config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        foreach ($this->getNumericConfigOptions() as $option => $defaultValue) {
            $value = $typeSpecificConfiguration->$option ?? $commercetoolsConfig->$option ?? $defaultValue;
            if ($value === null) {
                throw new \RuntimeException('Commercetools config option ' . $option . ' is not set');
            }
            if (!is_numeric($value)) {
                throw new \RuntimeException('Commercetools config option ' . $option . ' is not a number');
            }

            $config[$option] = $value;
        }

        return new Client(
            $config['clientId'],
            $config['clientSecret'],
            $config['projectKey'],
            $config['hostUrl'],
            $this->httpClient,
            $this->cache,
            (float)$config['readOperationTimeoutSeconds'],
            (float)$config['writeOperationTimeoutSeconds']
        );
    }

    /**
     * Create a `Commercetools\Client` of the given type for the given project. The type can for example be `product`,
     * `account`, `cart` or `wishlist`. Fo a complete list, see the `project.yml` config file.
     */
    public function factorForProjectAndType(Project $project, string $typeName): Client
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $commercetoolsConfig = $project->getConfigurationSection('commercetools');

        return $this->factorForConfigs($typeSpecificConfiguration, $commercetoolsConfig);
    }

    /**
     * @return array<string, string|null>
     */
    private function getStringConfigOptions(): array
    {
        return [
            'clientId' => null,
            'clientSecret' => null,
            'projectKey' => null,
            'hostUrl' => 'https://api.sphere.io', // provide a default value to keep BC
        ];
    }

    /**
     * @return array<string, int|null>
     */
    private function getNumericConfigOptions(): array
    {
        $environmentHttpTimeout = (int)getenv('http_client_timeout');

        return [
            'readOperationTimeoutSeconds' => max($environmentHttpTimeout, 2),
            'writeOperationTimeoutSeconds' => max($environmentHttpTimeout, 10),
        ];
    }
}
