<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\CachedShopwareProjectConfigApi;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApi;
use Psr\SimpleCache\CacheInterface;

class DefaultLocaleCreatorFactory extends LocaleCreatorFactory
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

    public function factor(Project $project, ClientInterface $client): LocaleCreator
    {
        return new DefaultLocaleCreator(
            new CachedShopwareProjectConfigApi(
                new ShopwareProjectConfigApi($client, $this->dataMapperResolver),
                $this->cache,
                $this->debug
            )
        );
    }
}
