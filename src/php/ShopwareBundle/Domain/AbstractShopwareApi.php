<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale;

abstract class AbstractShopwareApi
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ClientInterface
     */
    protected $client;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator
     */
    protected $localeCreator;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver
     */
    protected $mapperResolver;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale
     */
    protected $locale;

    /**
     * @var string
     */
    private $defaultLanguage;

    public function __construct(
        ClientInterface $client,
        DataMapperResolver $mapperResolver,
        ?LocaleCreator $localeCreator = null,
        ?string $defaultLanguage = null
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->defaultLanguage = $defaultLanguage;
    }

    protected function configureMapper(DataMapperInterface $mapper): void
    {
        if ($this->locale !== null && $mapper instanceof LocaleAwareDataMapperInterface) {
            $mapper->setLocale($this->locale);
        }
    }

    protected function mapResponse($response, string $mapperName)
    {
        $mapper = $this->mapperResolver->getMapper($mapperName);

        $this->configureMapper($mapper);

        return $mapper->map($response);
    }

    protected function mapRequestData($requestData, string $mapperName)
    {
        $mapper = $this->mapperResolver->getMapper($mapperName);

        $this->configureMapper($mapper);

        return $mapper->map($requestData);
    }

    protected function parseLocaleString(?string $localeString): ?ShopwareLocale
    {
        $localeString = $localeString ?? $this->defaultLanguage;

        if ($this->localeCreator === null) {
            return null;
        }

        $this->locale = $this->localeCreator->createLocaleFromString($localeString);

        return $this->locale;
    }
}
