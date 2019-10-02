<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\Container\ContainerInterface;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(ContainerInterface $container, Cache $cache)
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    public function factor(Project $project): ProjectApi
    {
        $productConfig = $project->configuration['product'];
        if (is_array($productConfig)) {
            $productConfig = (object)$productConfig;
        }

        switch ($productConfig->engine) {
            case 'commercetools':
                return new ProjectApi\Commercetools(
                    new ProductApi\Commercetools\Client(
                        $productConfig->clientId,
                        $productConfig->clientSecret,
                        $productConfig->projectKey,
                        $this->container->get(HttpClient::class),
                        $this->cache
                    ),
                    $project->languages
                );
        }

        throw new \OutOfBoundsException(
            "No product API configured for project {$project->name}. " .
            "Check the provisioned customer configuration in app/config/customers/."
        );
    }
}
