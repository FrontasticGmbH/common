<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Doctrine\Common\Cache\Cache;

use Frontastic\Common\HttpClient\Stream;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Factory
 */
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
        switch ($customer->configuration['cart']->engine) {
            case 'commercetools':
                $cartApi = new CartApi\Commercetools(
                    new Client(
                        $customer->configuration['cart']->clientId,
                        $customer->configuration['cart']->clientSecret,
                        $customer->configuration['cart']->projectKey,
                        $this->container->get(Stream::class),
                        $this->cache
                    ),
                    new Mapper(),
                    $this->getOrderIdGenerator()
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

    private function getOrderIdGenerator(): OrderIdGenerator
    {
        try {
            return $this->container->get('frontastic.order-id-generator');
        } catch (\Exception $e) {
            return new OrderIdGenerator\Random();
        }
    }
}
