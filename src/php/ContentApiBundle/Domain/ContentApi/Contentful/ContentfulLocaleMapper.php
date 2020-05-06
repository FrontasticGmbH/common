<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful;

use Contentful\Delivery\Client;
use Contentful\Delivery\Resource\Locale;
use Psr\SimpleCache\CacheInterface;

class ContentfulLocaleMapper implements LocaleMapper
{
    /** @var CacheInterface */
    private $cache;

    /** @var Client */
    private $client;

    /** @var Locale[]|null */
    private $contentfulLocales;

    public function __construct(CacheInterface $cache, Client $client)
    {
        $this->cache = $cache;
        $this->client = $client;
    }

    /**
     * @param string $frontasticLocale
     * @return string Locale to be sent to the CMS (frontastic format)
     */
    public function replaceLocale(string $frontasticLocale): string
    {
        $defaultContentfulLocale = null;

        // contentful doesn't know anything about currency, so remove it
        $withoutCurrency = explode('@', $frontasticLocale)[0];

        $frontasticLocale = str_replace(
            '_',
            '-',
            $withoutCurrency
        );

        foreach ($this->getContentfulLocales() as $locale) {
            if ($locale->isDefault()) {
                $defaultContentfulLocale = $locale->getCode();
            }
            if ($frontasticLocale === $locale->getCode()) {
                return $locale->getCode();
            }
        }

        $lang = \explode('-', $frontasticLocale)[0];
        foreach ($this->getContentfulLocales() as $locale) {
            $contentfulLang = explode('-', $locale->getCode())[0];
            if ($lang === $contentfulLang) {
                return $locale->getCode();
            }
        }

        return $defaultContentfulLocale;
    }

    /**
     * @return Locale[]
     */
    private function getContentfulLocales(): array
    {
        if ($this->contentfulLocales !== null) {
            return $this->contentfulLocales;
        }

        $cacheKey = 'contentful.' . $this->client->getSpaceId() . '.locales';

        $this->contentfulLocales = $this->cache->get($cacheKey);
        if ($this->contentfulLocales === null) {
            $this->contentfulLocales = $this->client->getEnvironment()->getLocales();
            $this->cache->set($cacheKey, $this->contentfulLocales);
        }

        return $this->contentfulLocales;
    }
}
