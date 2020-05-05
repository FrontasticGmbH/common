<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClient;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProjectConfigApi;
use Psr\SimpleCache\CacheInterface;

class DefaultSapLocaleCreatorFactory extends SapLocaleCreatorFactory
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function factor(Project $project, SapClient $client): SapLocaleCreator
    {
        return new DefaultSapLocaleCreator(
            new SapProjectConfigApi($client, $this->cache)
        );
    }
}
