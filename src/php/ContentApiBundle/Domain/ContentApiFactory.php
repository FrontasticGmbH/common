<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Doctrine\Common\Cache\Cache;

use Commercetools\Core\Client;
use Commercetools\Core\Config;
use Commercetools\Core\Model\Common\Context;

use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class ContentApiFactory
{
    private $container;
    private $decorators = [];

    public function __construct($container, iterable $decorators)
    {
        $this->container = $container;
        $this->decorators = $decorators;
    }

    public function factor(Customer $customer): ContentApi
    {
        switch (true) {
            case isset($customer->configuration['contentful']):
                $client = new \Contentful\Delivery\Client(
                    $customer->configuration['contentful']->accessToken,
                    $customer->configuration['contentful']->spaceId
                );
                $api = new ContentApi\Contentful($client);
                break;

            default:
                throw new \OutOfBoundsException(
                    "No content API configured for customer {$customer->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new ContentApi\LifecycleEventDecorator($api, $this->decorators);
    }
}
