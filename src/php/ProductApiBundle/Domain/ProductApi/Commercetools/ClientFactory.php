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

    /**
     * Create a `Commercetools\Client` of the given type for the given project. The type can for example be `product`,
     * `account`, `cart` or `wishlist`. Fo a complete list, see the `project.yml` config file.
     */
    public function factorForProjectAndType(Project $project, string $typeName): Client
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $commercetoolsConfig = $project->getConfigurationSection('commercetools');

        $config = [];
        foreach (['clientId', 'clientSecret', 'projectKey'] as $option) {
            $value = $typeSpecificConfiguration->$option ?? $commercetoolsConfig->$option ?? null;
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

        return new Client(
            $config['clientId'],
            $config['clientSecret'],
            $config['projectKey'],
            $this->httpClient,
            $this->cache
        );
    }
}
