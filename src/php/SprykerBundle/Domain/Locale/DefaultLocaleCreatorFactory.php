<?php

namespace Frontastic\Common\SprykerBundle\Domain\Locale;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\Domain\ProjectConfig\SprykerProjectConfigApi;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;

class DefaultLocaleCreatorFactory extends LocaleCreatorFactory
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function factor(Project $project, SprykerClientInterface $client): LocaleCreator
    {
        return new DefaultLocaleCreator(
            new SprykerProjectConfigApi($client, $this->cache)
        );
    }
}
