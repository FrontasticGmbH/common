<?php

namespace Frontastic\Common\SprykerBundle\Domain\Locale;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\Domain\ProjectConfig\SprykerProjectConfigApi;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Psr\SimpleCache\CacheInterface;

class DefaultLocaleCreatorFactory extends LocaleCreatorFactory
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
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
