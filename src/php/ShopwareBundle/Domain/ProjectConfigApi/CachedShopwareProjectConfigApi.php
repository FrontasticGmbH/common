<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Psr\SimpleCache\CacheInterface;

class CachedShopwareProjectConfigApi implements ShopwareProjectConfigApiInterface
{
    private const DEFAULT_CACHE_TTL = 600;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface
     */
    private $aggregate;

    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    /**
     * @var int
     */
    private $cacheTtl;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(
        ShopwareProjectConfigApiInterface $aggregate,
        CacheInterface $cache,
        bool $debug = false,
        int $cacheTtl = self::DEFAULT_CACHE_TTL
    ) {
        $this->aggregate = $aggregate;
        $this->cache = $cache;
        $this->debug = $debug;
        $this->cacheTtl = $cacheTtl;
    }

    public function getProjectConfig(): array
    {
        $cacheKey = $this->buildCacheKey(__FUNCTION__);

        if ($this->debug || false === ($result = $this->cache->get($cacheKey, false))) {
            $result = $this->aggregate->getProjectConfig();
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    private function buildCacheKey(string $resource): string
    {
        return sprintf('frontastic.common.shopware.project-config-api.%s', $resource);
    }
}
