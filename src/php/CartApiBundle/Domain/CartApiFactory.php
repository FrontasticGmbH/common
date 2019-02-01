<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Doctrine\Common\Cache\Cache;

use Frontastic\Common\HttpClient\Stream;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class CartApiFactory
{
    private $container;
    private $cache;
    private $decorators = [];

    public function __construct($container, Cache $cache, iterable $decorators)
    {
        $this->container = $container;
        $this->cache = $cache;
        $this->decorators = $decorators;
    }

    public function factor(Customer $customer): CartApi
    {
        switch (true) {
            case isset($customer->configuration['commercetools']):
                $cartApi = new CartApi\Commercetools(
                    new Client(
                        $customer->configuration['commercetools']->clientId,
                        $customer->configuration['commercetools']->clientSecret,
                        $customer->configuration['commercetools']->projectKey,
                        $this->container->get(Stream::class),
                        $this->cache
                    ),
                    new Mapper(),
                    // @todo: Should come out of the container
                    new OrderIdGenerator\Random()
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No cart API configured for customer {$customer->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new CartApi\LifecycleEventDecorator($cartApi, $this->decorators);
    }
}
