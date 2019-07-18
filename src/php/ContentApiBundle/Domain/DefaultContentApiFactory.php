<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient\Guzzle;

use Commercetools\Core\Client;
use Commercetools\Core\Config;
use Commercetools\Core\Model\Common\Context;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

class DefaultContentApiFactory implements ContentApiFactory
{
    private $container;
    private $decorators = [];

    public function __construct($container, iterable $decorators)
    {
        $this->container = $container;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): ContentApi
    {
        // make sure the config is an object, not an array
        $contentConfiguration = json_decode(json_encode($project->configuration['content']), false);

        switch ($contentConfiguration->engine) {
            case 'contentful':
                $client = new \Contentful\Delivery\Client(
                    $contentConfiguration->accessToken,
                    $contentConfiguration->spaceId
                );
                $api = new ContentApi\Contentful($client, $project->defaultLanguage);
                break;
            case 'graphcms':
                $client = new ContentApi\GraphCMS\Client(
                    $contentConfiguration->projectId,
                    $contentConfiguration->apiToken,
                    $contentConfiguration->region,
                    $contentConfiguration->stage,
                    new Guzzle()
                );
                $api = new ContentApi\GraphCMS($client);
                break;
            default:
                throw new \OutOfBoundsException(
                    "No content API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new ContentApi\LifecycleEventDecorator($api, $this->decorators);
    }
}
