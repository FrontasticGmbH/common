<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Contentful\RichText\Renderer;
use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\ContentfulLocaleMapper;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\CachingContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\NoopLocaleMapper;
use Frontastic\Common\HttpClient\Guzzle;

use Commercetools\Core\Client;
use Commercetools\Core\Config;
use Commercetools\Core\Model\Common\Context;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\SimpleCache\CacheInterface;
use Psr\Container\ContainerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultContentApiFactory implements ContentApiFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;
    private $decorators = [];
    private $contentfulLocaleMapperId = 'Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\LocaleMapper';

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * @var CacheInterface
     */
    private $psrCache;

    /**
     * @var Renderer
     */
    private $richtextRenderer;

    public function __construct(
        ContainerInterface $container,
        Cache $cache,
        CacheInterface $psrCache,
        Renderer $richtextRenderer,
        iterable $decorators
    ) {
        $this->container = $container;
        $this->decorators = $decorators;
        $this->cache = $cache;
        $this->psrCache = $psrCache;
        $this->richtextRenderer = $richtextRenderer;
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

                if ($this->container->has($this->contentfulLocaleMapperId)) {
                    $localeMapper = $this->container->get($this->contentfulLocaleMapperId);
                } else {
                    $localeMapper = new ContentfulLocaleMapper($this->psrCache, $client);
                }

                $api = new ContentApi\Contentful(
                    $client,
                    $this->richtextRenderer,
                    $localeMapper,
                    $project->defaultLanguage,
                    $this->cache
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
            $this->psrCache,
            $contentConfiguration->cacheTtlSec ?? 600
        );
    }
}
