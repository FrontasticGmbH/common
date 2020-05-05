<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Psr\SimpleCache\CacheInterface;

class CachedShopwareProjectConfigApi implements ShopwareProjectConfigApiInterface
{
    private const CACHE_KEY_FORMAT = 'frontastic.common.shopware.project-config-api.%s';
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

    /**
     * @inheritDoc
     */
    public function getCountryByCriteria(string $criteria): ?ShopwareCountry
    {
        $cacheKey = $this->buildCacheKey(__FUNCTION__, $criteria);

        if ($this->debug || false === ($result = $this->cache->get($cacheKey, false))) {
            $result = $this->aggregate->getCountryByCriteria($criteria);
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethods(): array
    {
        $cacheKey = $this->buildCacheKey(__FUNCTION__);

        if ($this->debug || false === ($result = $this->cache->get($cacheKey, false))) {
            $result = $this->aggregate->getPaymentMethods();
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getProjectConfig(): array
    {
        $cacheKey = $this->buildCacheKey(__FUNCTION__);

        if ($this->debug || false === ($result = $this->cache->get($cacheKey, false))) {
            $result = $this->aggregate->getProjectConfig();
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getSalutation(string $criteria): ?ShopwareSalutation
    {
        $cacheKey = $this->buildCacheKey(__FUNCTION__, $criteria ?? '_empty_');

        if ($this->debug || false === ($result = $this->cache->get($cacheKey, false))) {
            $result = $this->aggregate->getSalutation($criteria);
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getSalutations(?string $criteria = null): array
    {
        $cacheKey = $this->buildCacheKey(__FUNCTION__, $criteria ?? '_empty_');

        if ($this->debug || false === ($result = $this->cache->get($cacheKey, false))) {
            $result = $this->aggregate->getSalutations($criteria);
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethods(): array
    {
        $cacheKey = $this->buildCacheKey(__FUNCTION__);

        if ($this->debug || false === ($result = $this->cache->get($cacheKey, false))) {
            $result = $this->aggregate->getShippingMethods();
            $this->cache->set($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }

    private function buildCacheKey(string ...$parts): string
    {
        $suffix = implode('.', $parts);
        return sprintf(self::CACHE_KEY_FORMAT, $suffix);
    }
}
