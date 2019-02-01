<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Doctrine\Common\Cache\Cache;

use Frontastic\Common\HttpClient\Stream;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class AccountApiFactory
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

    public function factor(Customer $customer): AccountApi
    {
        switch (true) {
            case isset($customer->configuration['commercetools']):
                $accountApi = new AccountApi\Commercetools(
                    new Client(
                        $customer->configuration['commercetools']->clientId,
                        $customer->configuration['commercetools']->clientSecret,
                        $customer->configuration['commercetools']->projectKey,
                        $this->container->get(Stream::class),
                        $this->cache
                    )
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No account API configured for customer {$customer->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new AccountApi\LifecycleEventDecorator($accountApi, $this->decorators);
    }
}
