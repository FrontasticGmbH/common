<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use GuzzleHttp\Promise;
use Psr\SimpleCache\CacheInterface;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

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

    /**
     * @var int
     */
    private $cacheTtl;

    /**
     * @var bool
     */
    private $debug;

    /**
     * Warning - configuring the cacheTtl is considered experimental and subject to change.
     */
    public function __construct(
        ContentApi $aggregate,
        CacheInterface $cache,
        int $cacheTtlSec = 600,
        bool $debug = false
    ) {
        $this->aggregate = $aggregate;
        $this->cache = $cache;
        $this->cacheTtl = $cacheTtlSec;
        $this->debug = $debug;
    }

    public function getContentTypes(): array
    {
        // Currently not caching this, so that updates to this are visible faster,
        // and this is currently not a performance problem.
        return $this->aggregate->getContentTypes();
    }

    public function getContent(string $contentId, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        if ($mode === self::QUERY_SYNC) {
            return $this->getContentCachedSynced($contentId, $locale);
        }

        return $this->getContentCachedAsync($contentId, $locale);
    }

    public function query(Query $query, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        if ($mode === self::QUERY_SYNC) {
            return $this->queryCachedSynced($query, $locale);
        }

        return $this->queryCachedAsync($query, $locale);
    }

    public function getDangerousInnerClient()
    {
        return $this->aggregate->getDangerousInnerClient();
    }

    private function getContentCachedSynced(string $contentId, string $locale)
    {
        $cacheKey = $this->generateCacheForContentKey($contentId, $locale);
        $result = $this->cache->get($cacheKey, false);

        if ($this->debug || $result === false) {
            $result = $this->aggregate->getContent($contentId, $locale);
            $result->dangerousInnerContent = null;
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    private function getContentCachedAsync(string $contentId, string $locale)
    {
        $cacheKey = $this->generateCacheForContentKey($contentId, $locale);
        $result = $this->cache->get($cacheKey, false);

        if ($this->debug || $result === false) {
            return $this->aggregate->getContent($contentId, $locale, self::QUERY_ASYNC)
                ->then(function ($result) use ($cacheKey) {
                    $result->dangerousInnerContent = null;
                    $this->cache->set($cacheKey, $result, $this->cacheTtl);

                    return $result;
                });
        }

        return Promise\promise_for($result);
    }

    /**
     * @param string $contentId
     * @param string $locale
     * @return string
     */
    private function generateCacheForContentKey(string $contentId, string $locale): string
    {
        return 'frontastic.content.content.' . md5($contentId) . '.' . md5($locale);
    }

    private function queryCachedSynced(Query $query, string $locale)
    {
        $cacheKey = $this->generateCacheKeyForQuery($query, $locale);

        $result = $this->cache->get($cacheKey, false);
        if ($this->debug || $result === false) {
            $result = $this->aggregate->query($query, $locale);

            /** @var Content $item */
            foreach ($result->items as $item) {
                $item->dangerousInnerContent = null;
            }
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    private function queryCachedAsync(Query $query, string $locale)
    {
        $cacheKey = $this->generateCacheKeyForQuery($query, $locale);
        $result = $this->cache->get($cacheKey, false);

        if ($this->debug || $result === false) {
            return $this->aggregate->query($query, $locale, self::QUERY_ASYNC)
                ->then(function ($result) use ($cacheKey) {
                    /** @var Content $item */
                    foreach ($result->items as $item) {
                        $item->dangerousInnerContent = null;
                    }
                    $this->cache->set($cacheKey, $result, $this->cacheTtl);

                    return $result;
                });
        }

        return Promise\promise_for($result);
    }

    /**
     * @param Query $query
     * @param string $locale
     * @return string
     */
    private function generateCacheKeyForQuery(Query $query, string $locale): string
    {
        $cacheKey = 'frontastic.content.query.' . md5(Json::encode($query)) . '.' . md5($locale);
        return $cacheKey;
    }
}
