<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ShopwareBundle\Domain\Client;
use Frontastic\Common\ShopwareBundle\Domain\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\CategoryMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductResultMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Query\QueryOptionExpander;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchCriteriaBuilder;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperInterface;

class ShopwareProductApi implements ProductApi
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Client
     */
    private $client;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator
     */
    private $localeCreator;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapperResolver
     */
    private $mapperResolver;

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Options
     */
    private $options;

    public function __construct(Client $client, DataMapperResolver $mapperResolver, LocaleCreator $localeCreator)
    {
        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->options = new Options();
    }

    /**
     * Overwrite default Shopware options.
     *
     * Explicitly NOT part of the ProductApi interface because Shopware specific and only to be used during
     * factoring!
     */
    public function overwriteOptions(array $newOptions): void
    {
        $this->options = new Options($newOptions);
    }

    public function getCategories(CategoryQuery $query): array
    {
        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        $criteria = SearchCriteriaBuilder::buildFromCategoryQuery($query);

        return $this->client->post('/category', [], [], $criteria)
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

        $identifier = $query->sku;
        $parameters = [];

        if ($identifier === null) {
            $identifier = $query->productId;
            $parameters = [
                'useNumberAsId' => true
            ];
        }

        // @TODO: build locale and pass it to request

        $promise = $this->client
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
        $query = QueryOptionExpander::expandQueryWithOptions($query, $this->options);
        $criteria = SearchCriteriaBuilder::buildFromProductQuery($query);

        $promise = $this->client
            ->post('/product', [], [], $criteria)
            ->then(function ($response) use ($query) {
                return $this->mapResponse($response, $query, ProductResultMapper::MAPPER_NAME);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    public function getDangerousInnerClient(): Client
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
