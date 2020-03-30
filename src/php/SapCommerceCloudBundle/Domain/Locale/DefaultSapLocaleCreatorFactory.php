<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain\Locale;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClient;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProjectConfigApi;

class DefaultSapLocaleCreatorFactory extends SapLocaleCreatorFactory
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
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
