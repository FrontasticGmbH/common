<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Psr\SimpleCache\CacheInterface;

class ShopwareProjectConfigApiFactory
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver
     */
    private $dataMapperResolver;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(CacheInterface $cache, DataMapperResolver $dataMapperResolver, bool $debug)
    {
        $this->cache = $cache;
        $this->dataMapperResolver = $dataMapperResolver;
        $this->debug = $debug;
    }

    /**
     * @inheritDoc
     */
    public function factor(
        ClientInterface $client,
        ?LocaleCreator $localeCreator = null
    ): ShopwareProjectConfigApiInterface {
        return new CachedShopwareProjectConfigApi(
            new ShopwareProjectConfigApi(
                $client,
                $this->dataMapperResolver,
                $localeCreator
            ),
            $this->cache,
            $this->debug
        );
    }
}
