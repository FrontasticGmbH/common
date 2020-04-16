<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\CategoryMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductResultMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Query\QueryFacetExpander;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchCriteriaBuilder;

class ShopwareProductApi implements ProductApi
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ClientInterface
     */
    private $client;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator
     */
    private $localeCreator;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver
     */
    private $mapperResolver;

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService
     */
    private $enabledFacetService;

    public function __construct(
        ClientInterface $client,
        LocaleCreator $localeCreator,
        DataMapperResolver $mapperResolver,
        EnabledFacetService $enabledFacetService
    ) {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->enabledFacetService = $enabledFacetService;
    }

    public function getCategories(CategoryQuery $query): array
    {
        $criteria = SearchCriteriaBuilder::buildFromCategoryQuery($query);

        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        return $this->client
            ->forLanguage($locale->languageId)
            ->post('/category', [], $criteria)
            ->then(function ($response) use ($query) {
                return $this->mapResponse($response, $query, CategoryMapper::MAPPER_NAME);
            })
            ->wait();
    }

    public function getProductTypes(ProductTypeQuery $query): array
    {
        return [];
    }

    public function getProduct($query, string $mode = self::QUERY_SYNC): ?object
    {
        $query = ProductApi\Query\SingleProductQuery::fromLegacyQuery($query);
        $query->validate();

        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        $identifier = $query->sku;
        $parameters = [];

        if ($identifier === null) {
            $identifier = $query->productId;
            $parameters = [
                'useNumberAsId' => true
            ];
        }

        $promise = $this->client
            ->forLanguage($locale->languageId)
            ->forCurrency($locale->currencyId)
            ->get("/product/{$identifier}", $parameters)
            ->then(function ($response) use ($query) {
                return $this->mapResponse($response, $query, ProductMapper::MAPPER_NAME);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        $query = QueryFacetExpander::expandQueryEnabledFacets(
            $query,
            $this->enabledFacetService->getEnabledFacetDefinitions()
        );
        $criteria = SearchCriteriaBuilder::buildFromProductQuery($query);

        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        $promise = $this->client
            ->forLanguage($locale->languageId)
            ->forCurrency($locale->currencyId)
            ->post('/product', [], $criteria)
            ->then(function ($response) use ($query) {
                return $this->mapResponse($response, $query, ProductResultMapper::MAPPER_NAME);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    public function getDangerousInnerClient(): ClientInterface
    {
        return $this->client;
    }

    private function mapResponse(array $response, ProductApi\Query $query, string $mapperName)
    {
        $mapper = $this->mapperResolver->getMapper($mapperName);

        if ($mapper instanceof QueryAwareDataMapperInterface) {
            $mapper->setQuery($query);
        }

        return $mapper->map($response);
    }
}
