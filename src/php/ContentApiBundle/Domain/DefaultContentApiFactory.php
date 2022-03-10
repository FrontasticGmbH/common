<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Contentful\RichText\Renderer;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\CachingContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\ContentfulClientFactory;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\ContentfulLocaleMapper;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\GraphCMSClientFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultContentApiFactory implements ContentApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'content';

    /**
     * @var ContainerInterface
     */
    private $container;
    private $decorators = [];
    private $contentfulLocaleMapperId = 'Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\LocaleMapper';

    /**
     * @var CacheInterface
     */
    private $psrCache;

    /**
     * @var Renderer
     */
    private $richtextRenderer;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(
        ContainerInterface $container,
        CacheInterface $psrCache,
        Renderer $richtextRenderer,
        bool $debug,
        iterable $decorators
    ) {
        $this->container = $container;
        $this->decorators = $decorators;
        $this->psrCache = $psrCache;
        $this->richtextRenderer = $richtextRenderer;
        $this->debug = $debug;
    }

    public function factor(Project $project): ContentApi
    {
        $contentConfiguration = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME);

        switch ($contentConfiguration->engine) {
            case 'contentful':
                $clientFactory = $this->container->get(ContentfulClientFactory::class);
                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                if ($this->container->has($this->contentfulLocaleMapperId)) {
                    $localeMapper = $this->container->get($this->contentfulLocaleMapperId);
                } else {
                    $localeMapper = new ContentfulLocaleMapper($this->psrCache, $client);
                }

                $api = new ContentApi\Contentful(
                    $client,
                    $this->richtextRenderer,
                    $localeMapper,
                    $project->defaultLanguage
                );
                break;

            case 'graphcms':
                $clientFactory = $this->container->get(GraphCMSClientFactory::class);
                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
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
            $contentConfiguration->cacheTtlSec ?? 600,
            $this->debug
        );
    }
}
