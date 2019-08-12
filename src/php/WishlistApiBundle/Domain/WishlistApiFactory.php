<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Doctrine\Common\Cache\Cache;

use Frontastic\Common\HttpClient\Stream;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
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
        /* @var ProductApiFactory $productApiFactory */
        $productApiFactory = $this->container->get(ProductApiFactory::class);

        switch ($customer->configuration['wishlist']->engine) {
            case 'commercetools':
                $wishlistApi = new WishlistApi\Commercetools(
                    new Client(
                        $customer->configuration['wishlist']->clientId,
                        $customer->configuration['wishlist']->clientSecret,
                        $customer->configuration['wishlist']->projectKey,
                        $this->container->get(Stream::class),
                        $this->cache
                    ),
                    $productApiFactory->factor($customer)
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
