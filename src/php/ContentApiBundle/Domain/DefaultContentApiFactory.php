<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;

use Commercetools\Core\Client;
use Commercetools\Core\Config;
use Commercetools\Core\Model\Common\Context;
use Contentful\RichText\Renderer;

use Frontastic\Common\ReplicatorBundle\Domain\Project;

class DefaultContentApiFactory implements ContentApiFactory
{
    private $container;
    private $decorators = [];

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    public function __construct($container, Cache $cache, iterable $decorators)
    {
        $this->container = $container;
        $this->decorators = $decorators;
        $this->cache = $cache;
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
                $api = new ContentApi\Contentful(
                    $client,
                    new Renderer(),
                    $project->defaultLanguage
                );
                break;
            case 'graphcms':
                $client = new ContentApi\GraphCMS\Client(
                    $contentConfiguration->projectId,
                    $contentConfiguration->apiToken,
                    $contentConfiguration->region,
                    $contentConfiguration->stage,
                    $this->container->get(HttpClient::class),
                    $this->cache
                );
                $api = new ContentApi\GraphCMS($client, $project->defaultLanguage);
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
