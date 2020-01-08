<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ProjectConfigApi;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class DefaultCommercetoolsLocaleCreatorFactory extends CommercetoolsLocaleCreatorFactory
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function factor(Project $project, Client $client): CommercetoolsLocaleCreator
    {
        return new DefaultCommercetoolsLocaleCreator(
            new ProjectConfigApi(
                $client,
                $this->cache
            )
        );
    }
}
