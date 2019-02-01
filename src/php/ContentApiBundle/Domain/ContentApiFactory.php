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

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function factor(Customer $customer): ContentApi
    {
        switch (true) {
            case isset($customer->configuration['contentful']):
                $client = new \Contentful\Delivery\Client(
                    $customer->configuration['contentful']->accessToken,
                    $customer->configuration['contentful']->spaceId
                );
                return new ContentApi\Contentful($client);
            default:
                throw new \OutOfBoundsException(
                    "No content API configured for customer {$customer->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }
    }
}
