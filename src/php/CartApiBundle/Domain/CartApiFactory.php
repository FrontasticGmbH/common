<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Factory
 */
class CartApiFactory
{
    private $container;

    /**
     * @var OrderIdGenerator
     */
    private $orderIdGenerator;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var iterable
     */
    private $decorators = [];

    public function __construct($container, OrderIdGenerator $orderIdGenerator, Cache $cache, iterable $decorators)
    {
        $this->container = $container;
        $this->orderIdGenerator = $orderIdGenerator;
        $this->cache = $cache;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): CartApi
    {
        $cartConfig = $project->getConfigurationSection('cart');

        switch ($cartConfig->engine) {
            case 'commercetools':
                $cartApi = new CartApi\Commercetools(
                    new Client(
                        $cartConfig->clientId,
                        $cartConfig->clientSecret,
                        $cartConfig->projectKey,
                        $this->container->get(HttpClient::class),
                        $this->cache
                    ),
                    new Mapper(),
                    $this->orderIdGenerator
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No cart API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new CartApi\LifecycleEventDecorator($cartApi, $this->decorators);
    }
}
