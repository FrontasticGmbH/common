<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectConfigApi as ProjectConfigApiInterface;

class ProjectConfigApi implements ProjectConfigApiInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $cacheTtl;

    public function __construct(Client $client, Cache $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->cacheTtl = 600;
    }

    public function getProjectConfig(): array
    {
        $cacheKey = sprintf(
            'frontastic.commercetools.projectConfig.%s',
            $this->client->getProjectKey()
        );

        $result = $this->cache->fetch($cacheKey);
        if ($result !== false) {
            return $result;
        }

        $result = $this->client->get('');
        $this->cache->save($cacheKey, $result, $this->cacheTtl);
        return $result;
    }
}
