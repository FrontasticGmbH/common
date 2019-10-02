<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
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
        switch ($customer->configuration['account']->engine) {
            case 'commercetools':
                $accountApi = new AccountApi\Commercetools(
                    new Client(
                        $customer->configuration['account']->clientId,
                        $customer->configuration['account']->clientSecret,
                        $customer->configuration['account']->projectKey,
                        $this->container->get(HttpClient::class),
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
