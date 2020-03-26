<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectConfigApi;

class CachedShopwareProjectApi implements ProjectApi, ProjectConfigApi
{
    private const DEFAULT_CACHE_TTL = 600;

    /**
     * @var \Frontastic\Common\ProjectApiBundle\Domain\ProjectConfigApi|\Frontastic\Common\ProjectApiBundle\Domain\ProjectConfigApi
     */
    private $aggregate;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $cacheTtl;

    public function __construct(ProjectApi $aggregate, Cache $cache, int $cacheTtl = self::DEFAULT_CACHE_TTL)
    {
        $this->aggregate = $aggregate;
        $this->cache = $cache;
        $this->cacheTtl = $cacheTtl;
    }

    public function getProjectConfig(): array
    {
        $cacheKey = $this->buildCacheKey(__METHOD__);

        $result = $this->cache->fetch($cacheKey);
        if ($result !== false) {
            return $result;
        }

        $result = $this->aggregate->getProjectConfig();
        $this->cache->save($cacheKey, $result, $this->cacheTtl);

        return $result;
    }

    /**
     * @return Attribute[] Attributes mapped by ID
     */
    public function getSearchableAttributes(): array
    {
        $cacheKey = $this->buildCacheKey(__METHOD__);

        $result = $this->cache->fetch($cacheKey);
        if ($result !== false) {
            return $result;
        }

        $result = $this->aggregate->getSearchableAttributes();
        $this->cache->save($cacheKey, $result, $this->cacheTtl);

        return $result;
    }

    private function buildCacheKey(string $resource): string
    {
        return sprintf(
            'frontastic.shopware.%s.%s',
            __CLASS__,
            $resource
        );
    }
}
