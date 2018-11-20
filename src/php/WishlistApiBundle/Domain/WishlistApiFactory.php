<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Doctrine\Common\Cache\ArrayCache;

use Frontastic\Common\HttpClient\Stream;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class WishlistApiFactory
{
    private $container;
    private $listeners = [];

    public function __construct($container, iterable $listeners)
    {
        $this->container = $container;
        $this->listeners = $listeners;
    }

    public function factor(Customer $customer): WishlistApi
    {
        switch (true) {
            case isset($customer->configuration['commercetools']):
                // @todo These objects should come from the DI Container
                $httpClient = new Stream();
                // @todo Use a persistent cache backend here.
                $cache = new ArrayCache();

                $wishlistApi = new WishlistApi\Commercetools(
                    new Client(
                        $customer->configuration['commercetools']->clientId,
                        $customer->configuration['commercetools']->clientSecret,
                        $customer->configuration['commercetools']->projectKey,
                        $httpClient,
                        $cache
                    )
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No wishlist API configured for customer {$customer->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new WishlistApi\LifecycleEventDecorator($wishlistApi, $this->listeners);
    }
}
