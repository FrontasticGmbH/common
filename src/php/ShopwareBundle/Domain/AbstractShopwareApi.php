<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;

abstract class AbstractShopwareApi
{
    public const TOKEN_TYPE = 'shopware';
    protected const KEY_CONTEXT_TOKEN = 'sw-context-token';

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

    public function __construct(
        ClientInterface $client,
        DataMapperResolver $mapperResolver,
        LocaleCreator $localeCreator = null
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
    }

    protected function configureMapper(DataMapperInterface $mapper): void
    {
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
}
