<?php

namespace Frontastic\Common\SprykerBundle\BaseApi;

use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\Locale\SprykerLocale;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

/**
 * TODO: Get rid off this class by using dedicated service SprykerUrlAppender and
 * implement mapper resolver strategy within each Api implementation.
 */
class SprykerApiBase
{
    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface
     */
    protected $client;

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\MapperResolver
     */
    protected $mapperResolver;

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator
     */
    protected $localeCreator;

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Locale\SprykerLocale
     */
    protected $locale;

    /**
     * @var string
     */
    private $defaultLanguage;

    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        ?LocaleCreator $localeCreator = null,
        ?string $defaultLanguage = null
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @param JsonApiResponse $response
     * @param string $mapperName
     *
     * @return mixed
     */
    protected function mapResponseResource(JsonApiResponse $response, string $mapperName)
    {
        $document = $response->document();
        $mapper = $this->mapperResolver->getMapper($mapperName);

        if ($document->isSingleResourceDocument()) {
            return $mapper->mapResource($document->primaryResource());
        }

        return $mapper->mapResource($document->primaryResources()[0]);
    }

    protected function getSeparator(string $url): string
    {
        return (strpos($url, '?') === false) ? '?' : '&';
    }

    protected function appendCurrencyToUrl(string $url, string $currency): string
    {
        $separator = $this->getSeparator($url);

        return "{$url}{$separator}currency={$currency}";
    }

    /**
     * @param string $url
     * @param array $includes
     *
     * @return string
     */
    protected function withIncludes(string $url, array $includes = []): string
    {
        if (empty($includes)) {
            return $url;
        }

        $separator = $this->getSeparator($url);
        $includesString = implode(',', $includes);

        return "{$url}{$separator}include={$includesString}";
    }

    protected function parseLocaleString(?string $localeString): SprykerLocale
    {
        $localeString = $localeString ?? $this->defaultLanguage;

        $this->locale = $this->localeCreator->createLocaleFromString($localeString);

        return $this->locale;
    }
}
