<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ShopwareBundle\Domain\Client;
use Frontastic\Common\ShopwareBundle\Domain\ProjectApi\ShopwareProjectApi;

class LocaleCreatorFactory
{
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function factor(Project $project, Client $client): LocaleCreator
    {
        return new LocaleCreator(
            new ShopwareProjectApi(
                $client,
                $this->cache
            )
        );
    }
}
