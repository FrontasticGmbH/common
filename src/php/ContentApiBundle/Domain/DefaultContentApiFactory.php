<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\CachingContentApi;
use Contentful\RichText\Renderer;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\SimpleCache\CacheInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultContentApiFactory implements ContentApiFactory
{
    private $container;
    private $decorators = [];

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * @var CacheInterface
     */
    private $psrCache;

    public function __construct($container, Cache $cache, CacheInterface $psrCache, iterable $decorators)
    {
        $this->container = $container;
        $this->decorators = $decorators;
        $this->cache = $cache;
        $this->psrCache = $psrCache;
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

        return new CachingContentApi(
            new ContentApi\LifecycleEventDecorator($api, $this->decorators),
            $this->psrCache
        );
    }
}
