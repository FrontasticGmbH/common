<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\FindologicBundle\Domain\FindologicClientFactory;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\FindologicProductSearchApi;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\Container\ContainerInterface;

class DefaultProductSearchApiFactory implements ProductSearchApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'product';

    /** @var ContainerInterface */
    private $container;

    /** @var array */
    private $decorators;

    public function __construct(
        ContainerInterface $container,
        iterable $decorators = []
    ) {
        $this->container = $container;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): ProductSearchApi
    {
        $productConfig = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME);

        switch ($productConfig->engine) {
            case 'findologic':
                $clientFactory = $this->container->get(FindologicClientFactory::class);
                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $productSearchApi = new FindologicProductSearchApi($client, new NoopProductSearchApi());
                break;
            default:
                throw new \OutOfBoundsException(
                    "No product search API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new LifecycleEventDecorator($productSearchApi, $this->decorators);
    }
}
