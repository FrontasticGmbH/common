<?php

namespace Frontastic\Common\SprykerBundle\Domain\ProjectConfig;

use Frontastic\Common\SprykerBundle\Domain\SprykerClient;
use Psr\SimpleCache\CacheInterface;

class SprykerProjectConfigApi
{
    public const RESOURCE_COUNTRIES = 'countries';
    public const RESOURCE_CURRENCIES = 'currencies';
    public const RESOURCE_LOCALES = 'locales';

    /** @var SprykerClient */
    private $client;

    /** @var CacheInterface */
    private $cache;

    /** @var int */
    private $cacheTtl;

    public function __construct(SprykerClient $client, CacheInterface $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->cacheTtl = 600;
    }

    public function getProjectConfig(): array
    {
        $cacheKey = sprintf(
            'frontastic.spryker.projectConfig.%s',
            $this->client->getProjectKey()
        );

        $projectConfig = $this->cache->get($cacheKey);
        if ($projectConfig !== null) {
            return $projectConfig;
        }

        $response = $this->client->get("/stores/{$this->client->getProjectKey()}");
        $resource = $response->document()->primaryResource()->toArray();
        $projectConfig = $resource['attributes'];

        $this->cache->set($cacheKey, $projectConfig, $this->cacheTtl);

        return $projectConfig;
    }
}
