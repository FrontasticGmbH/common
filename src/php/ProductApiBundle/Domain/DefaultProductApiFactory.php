<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultProductApiFactory implements ProductApiFactory
{
    /**
     * @var
     */
    private $container;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $decorators;

    public function __construct($container, Cache $cache, iterable $decorators = [])
    {
        $this->container = $container;
        $this->cache = $cache;
        $this->decorators = $decorators;
    }

    public function factor(Customer $customer): ProductApi
    {
        try {
            return new ProductApi\LifecycleEventDecorator(
                $this->factorFromConfiguration($customer->configuration),
                $this->decorators
            );
        } catch (\OutOfBoundsException $e) {
            throw new \OutOfBoundsException(
                "No product API configured for customer {$customer->name}. " .
                "Check the provisioned customer configuration in app/config/customers/.",
                0,
                $e
            );
        }
    }

    public function factorFromConfiguration(array $config): ProductApi
    {
        $productConfig = $config['product'];
        if (is_array($productConfig)) {
            $productConfig = (object)$productConfig;
        }

        switch ($productConfig->engine) {
            case 'commercetools':
                return new Commercetools(
                    new Commercetools\Client(
                        $productConfig->clientId,
                        $productConfig->clientSecret,
                        $productConfig->projectKey,
                        $this->container->get(HttpClient::class),
                        $this->cache
                    ),
                    new Commercetools\Mapper(
                        $config['commercetools']->localeOverwrite ?? null
                    ),
                    $config['commercetools']->localeOverwrite ?? null
                );
            default:
                throw new \OutOfBoundsException('No valid API configuration found');
        }
    }
}
