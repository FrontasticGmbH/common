<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;
use Psr\SimpleCache\CacheInterface;

class CachingContentApi implements ContentApi
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var ContentApi
     */
    private $aggregate;

    public function __construct(ContentApi $aggregate, CacheInterface $cache)
    {
        $this->aggregate = $aggregate;
        $this->cache = $cache;
    }

    public function getContentTypes(): array
    {
        // Currently not caching this, so that updates to this are visible faster,
        // and this is currently not a performance problem.
        return $this->aggregate->getContentTypes();
    }

    public function getContent(string $contentId, string $locale = null): Content
    {
        $cacheKey = 'frontastic.content.content.' . md5($contentId) . '.' . md5($locale);
        $result = $this->cache->get($cacheKey, false);

        if ($result === false) {
            $result = $this->aggregate->getContent($contentId, $locale);
            $result->dangerousInnerContent = null;
            $this->cache->set($cacheKey, $result, 600);
        }

        return $result;
    }

    public function query(Query $query, string $locale = null): Result
    {
        $cacheKey = 'frontastic.content.query.' . md5(json_encode($query)) . '.' . md5($locale);

        $result = $this->cache->get($cacheKey, false);
        if ($result === false) {
            $result = $this->aggregate->query($query, $locale);

            /** @var Content $item */
            foreach($result->items as $item) {
                $item->dangerousInnerContent = null;
            }
            $this->cache->set($cacheKey, $result, 600);
        }

        return $result;
    }

    public function getDangerousInnerClient()
    {
        return $this->aggregate->getDangerousInnerClient();
    }
}
