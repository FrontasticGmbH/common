<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Doctrine\Common\Cache\Cache;

use Frontastic\Common\HttpClient\Stream;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class WishlistApiFactory
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

    public function factor(Customer $customer): WishlistApi
    {
        switch (true) {
            case isset($customer->configuration['commercetools']):
                $wishlistApi = new WishlistApi\Commercetools(
                    new Client(
                        $customer->configuration['commercetools']->clientId,
                        $customer->configuration['commercetools']->clientSecret,
                        $customer->configuration['commercetools']->projectKey,
                        $this->container->get(Stream::class),
                        $this->cache
                    ),
                    new Mapper()
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No wishlist API configured for customer {$customer->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new WishlistApi\LifecycleEventDecorator($wishlistApi, $this->decorators);
    }
}
