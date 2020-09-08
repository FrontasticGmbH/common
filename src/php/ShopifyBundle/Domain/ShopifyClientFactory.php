<?php

namespace Frontastic\Common\ShopifyBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Psr\SimpleCache\CacheInterface;

class ShopifyClientFactory
{
    /** @var HttpClient */
    private $httpClient;

    /** @var CacheInterface */
    private $cache;

    public function __construct(HttpClient $httpClient, CacheInterface $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function factorForProjectAndType(Project $project, string $typeName): ShopifyClient
    {
        $typeSpecificConfiguration = $project->getConfigurationSection($typeName);
        $shopifyConfig = $project->getConfigurationSection('shopify');

        $config = [];
        foreach (['hostUrl', 'storefrontAccessToken'] as $option) {
            $value = $typeSpecificConfiguration->$option ?? $shopifyConfig->$option ?? null;

            if ($value === null) {
                throw new \RuntimeException('Shopify config option ' . $option . ' is not set');
            }
            if (!is_string($value)) {
                throw new \RuntimeException('Shopify config option ' . $option . ' is no string');
            }
            if ($value === '') {
                throw new \RuntimeException('Shopify config option ' . $option . ' is empty');
            }

            $config[$option] = $value;
        }

        return new ShopifyClient(
            $this->httpClient,
            $this->cache,
            $config['hostUrl'],
            $config['storefrontAccessToken']
        );
    }
}
