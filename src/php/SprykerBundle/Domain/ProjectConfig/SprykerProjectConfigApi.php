<?php

namespace Frontastic\Common\SprykerBundle\Domain\ProjectConfig;

use Frontastic\Common\SprykerBundle\Domain\SprykerClient;
use Psr\SimpleCache\CacheInterface;

class SprykerProjectConfigApi
{
    public const RESOURCE_COUNTRIES = 'countries';
    public const RESOURCE_CURRENCIES = 'currencies';
    public const RESOURCE_LANGUAGES = 'languages';

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

    /**
     * @return string[]
     */
    public function getLanguageCodes(): array
    {
        return $this->getIsoCodesFromProjectConfig('languages');
    }

    /**
     * @return string[]
     */
    public function getCurrencyCodes(): array
    {
        return $this->getIsoCodesFromProjectConfig('currencies');
    }

    private function getIsoCodesFromProjectConfig(string $configName): array
    {
        $cacheKey = sprintf('frontastic.spryker.%sCodes.%s', $configName, $this->client->getInstanceId());

        $result = $this->cache->get($cacheKey);
        if ($result !== null) {
            return $result;
        }

        $languages = $this->client->get('/rest/v2/{siteId}/' . $configName)->wait();
        $result = array_map(
            function (array $language): string {
                return $language['isocode'];
            },
            $languages[$configName]
        );
        $this->cache->set($cacheKey, $result, $this->cacheTtl);
        return $result;
    }
}