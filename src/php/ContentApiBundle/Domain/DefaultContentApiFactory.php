<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient\Guzzle;

use Commercetools\Core\Client;
use Commercetools\Core\Config;
use Commercetools\Core\Model\Common\Context;

use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class DefaultContentApiFactory implements ContentApiFactory
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
        switch ($customer->configuration['content']->engine) {
            case 'contentful':
                $client = new \Contentful\Delivery\Client(
                    $customer->configuration['content']->accessToken,
                    $customer->configuration['content']->spaceId
                );
                $api = new ContentApi\Contentful($client);
                break;
            case 'graphcms':
                $client = new ContentApi\GraphCMS\Client(
                    $customer->configuration['content']->projectId,
                    $customer->configuration['content']->apiToken,
                    new Guzzle()
                );
                $api = new ContentApi\GraphCMS($client);
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
