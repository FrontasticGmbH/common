<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ProjectConfigApi;

class CachedShopwareProjectConfigApi
{
    private const DEFAULT_CACHE_TTL = 600;

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ProjectConfigApi
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

    public function __construct(ProjectConfigApi $aggregate, Cache $cache, int $cacheTtl = self::DEFAULT_CACHE_TTL)
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

    private function buildCacheKey(string $resource): string
    {
        return sprintf(
            'frontastic.shopware.%s.%s',
            __CLASS__,
            $resource
        );
    }
}
