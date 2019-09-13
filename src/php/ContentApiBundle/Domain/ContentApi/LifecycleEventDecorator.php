<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Contentful\Delivery\Client;
use Contentful\Delivery\DynamicEntry;
use Contentful\Delivery\ContentType as ContentfulContentType;

use Frontastic\Common\LifecycleTrait;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;

class LifecycleEventDecorator implements ContentApi
{
    use LifecycleTrait;

    /**
     * @var \Frontastic\Common\WishlistApiBundle\Domain\WishlistApi
     */
    private $aggregate;

    /**
     * LifecycleEventDecorator constructor.
     *
     * @param \Frontastic\Common\WishlistApiBundle\Domain\WishlistApi $aggregate
     * @param iterable $listeners
     */
    public function __construct(ContentApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    public function getAggregate()
    {
        return $this->aggregate;
    }

    public function getContentTypes(): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getContent(string $contentId, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function query(Query $query, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getDangerousInnerClient()
    {
        return $this->aggregate->getDangerousInnerClient();
    }
}
