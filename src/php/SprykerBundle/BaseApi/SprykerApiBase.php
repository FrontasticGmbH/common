<?php

namespace Frontastic\Common\SprykerBundle\BaseApi;

use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\Locale\SprykerLocale;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

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

    /**
     * SprykerApiBase constructor.
     *
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface $client
     * @param \Frontastic\Common\SprykerBundle\Domain\MapperResolver $mapperResolver
     * @param LocaleCreator|null $localeCreator
     * @param string|null $defaultLanguage
     */
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

    /**
     * @param JsonApiResponse $response
     * @param string $mapperName
     *
     * @return array
     */
    protected function mapResponseArray(JsonApiResponse $response, string $mapperName): array
    {
        return $this->mapperResolver
            ->getExtendedMapper($mapperName)
            ->mapResourceArray($response->document()->primaryResources());
    }

    /**
     * @param string $url
     * @param array $includes
     *
     * @return string
     */
    protected function withIncludes(string $url, array $includes = []): string
    {
        if (count($includes) === 0) {
            return $url;
        }

        $separator = (strpos($url, '?') === false) ? '?' : '&';
        $includesString = implode(',', $includes);

        return "{$url}{$separator}include={$includesString}";
    }


    /**
     * @param string $json
     *
     * @return array
     */
    protected function mapErrors(string $json): array
    {
        $data = json_decode($json, true);

        if (isset($data['message'])) {
            $data = json_decode($data['message'], true);
        }

        return $data['errors'] ?? [];
    }

    protected function parseLocaleString(?string $localeString): SprykerLocale
    {
        $localeString = $localeString ?? $this->defaultLanguage;

        $this->locale = $this->localeCreator->createLocaleFromString($localeString);

        return $this->locale;
    }
}
