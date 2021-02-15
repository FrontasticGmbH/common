<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\LifecycleEventDecorator\BaseImplementation;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\LifecycleEventDecorator\BaseImplementationAdapterV2;
use Frontastic\Common\LifecycleTrait;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\Query;

class LifecycleEventDecorator implements ContentApi
{
    use LifecycleTrait;

    /**
     * @var ContentApi
     */
    private $aggregate;

    /**
     * LifecycleEventDecorator constructor.
     *
     * @param ContentApi $aggregate
     * @param iterable $listeners
     */
    public function __construct(ContentApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            if ($listener instanceof BaseImplementation) {
                $listener = new BaseImplementationAdapterV2($listener);
            }
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
