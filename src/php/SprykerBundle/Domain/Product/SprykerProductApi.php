<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\SprykerBundle\BaseApi\ProductExpandingTrait;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested\NestedSearchProductAbstractExpander;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\ProductConcreteMapper;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\CategoriesMapper;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\ProductMapper;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\ProductResultMapper;
use GuzzleHttp\Promise\PromiseInterface;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

class SprykerProductApi extends SprykerApiBase implements ProductApi
{
    use ProductExpandingTrait;

    /**
     * @var array
     */
    protected $productResources;

    /**
     * @var array
     */
    protected $concreteProductResources;

    /**
     * @var array
     */
    protected $queryResources;

    /**
     * SprykerProductApi constructor.
     *
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface $client
     * @param \Frontastic\Common\SprykerBundle\Domain\MapperResolver $mapperResolver
     * @param LocaleCreator $localeCreator
     * @param array $resources
     * @param array $queryResources
     * @param array $concreteProductResources
     */
    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        array $resources = SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_PRODUCT_RESOURCES,
        array $queryResources = SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES,
        array $concreteProductResources = SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_CONCRETE_PRODUCT_RESOURCES
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator);
        $this->productResources = $resources;
        $this->queryResources = $queryResources;
        $this->concreteProductResources = $concreteProductResources;

        $this->registerProductExpander(new NestedSearchProductAbstractExpander());
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery $query
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Category[]
     */
    public function getCategories(CategoryQuery $query): array
    {
        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        $response = $this->client
            ->forLanguage($locale->language)
            ->get('/category-trees');

        return $this->mapResponseResource($response, CategoriesMapper::MAPPER_NAME);
    }

    public function queryCategories(CategoryQuery $query): Result
    {
        $categories = $this->getCategories($query);

        return new Result([
            'count' => count($categories),
            'items' => $categories,
            'query' => clone($query)
        ]);
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery $query
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductType[]
     */
    public function getProductTypes(ProductTypeQuery $query): array
    {
        return [];
    }

    /**
     * @param $query
     * @param string $mode One of the QUERY_* connstants. Execute the query synchronously or asynchronously?
     * @return Product|PromiseInterface|null A product or null when the mode is sync and a promise if the mode is async.
     */
    public function getProduct($query, string $mode = ProductApi::QUERY_SYNC): ?object
    {
        $query = SingleProductQuery::fromLegacyQuery($query);
        $query->validate();

        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        $endpoint = $this->withIncludes("/abstract-products/{$query->productId}", $this->productResources);
        $mapperName = ProductMapper::MAPPER_NAME;

        if ($query->sku) {
            $endpoint = $this->withIncludes("/concrete-products/{$query->sku}", $this->concreteProductResources);
            $mapperName = ProductConcreteMapper::MAPPER_NAME;
        }

        $response = $this->client
            ->forLanguage($locale->language)
            ->get(
                $endpoint,
                [],
                ProductApi::QUERY_ASYNC
            )
            ->then(function ($response) use ($mapperName) {
                $product = $this->mapResponseResource($response, $mapperName);
                $resources = $this->getAllResources($response);

                if ($product && count($resources) > 0) {
                    $this->expandProduct($product, $resources);
                }

                return $product;
            })
            ->otherwise(function (\Throwable $exception) use ($query) {
                if ($exception instanceof RequestException && $exception->getCode() >= 500) {
                    return;
                }
                if ($exception instanceof RequestException && $exception->getCode() === 404) {
                    if ($query->sku !== null) {
                        throw ProductApi\ProductNotFoundException::bySku($query->sku);
                    }
                    throw ProductApi\ProductNotFoundException::byProductId($query->productId);
                }
                throw $exception;
            });

        if ($mode === ProductApi::QUERY_SYNC) {
            return $response->wait();
        }

        return $response;
    }

    /**
     * @param ProductQuery $query
     * @param string $mode One of the QUERY_* constants. Execute the query synchronously or asynchronously?
     * @return Result|PromiseInterface<Result> A result when the mode is sync and a promise if the mode is async.
     */
    public function query(ProductQuery $query, string $mode = ProductApi::QUERY_SYNC): object
    {
        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        $searchQuery = CatalogSearchQuery::createFromProductQuery($query) . "&currency=$locale->currency";
        $response = $this->client
            ->forLanguage($locale->language)
            ->get(
                $this->withIncludes("/catalog-search?{$searchQuery}", $this->queryResources),
                [],
                ProductApi::QUERY_ASYNC
            )
            ->then(function ($response) use ($query) {
                $products = $this->mapResponseResource($response, ProductResultMapper::MAPPER_NAME);
                $includedResources = $this->getAllResources($response) ?? [];

                if (count($products->items) && count($includedResources)) {
                    $this->expandProductList($products->items, $includedResources);
                }

                $products->query = clone $query;

                return $products;
            })
            ->otherwise(function (\Throwable $exception) use ($query) {
                if ($exception instanceof RequestException && $exception->getCode() >= 500) {
                    return new Result([
                        'query' => clone $query
                    ]);
                }
                throw $exception;
            });

        if ($mode === ProductApi::QUERY_SYNC) {
            return $response->wait();
        }

        return $response;
    }

    /**
     * @return SprykerClientInterface
     */
    public function getDangerousInnerClient(): SprykerClientInterface
    {
        return $this->client;
    }

    /**
     * @param SingleProductQuery $query
     *
     * @return string
     */
    protected function resolveProductIdentifier(SingleProductQuery $query): string
    {
        return $query->sku ?? $query->productId;
    }

    /**
     * @param \WoohooLabs\Yang\JsonApi\Response\JsonApiResponse $response
     *
     * @return \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[]
     */
    protected function getAllResources(JsonApiResponse $response): array
    {
        $resources = [];

        if (!$response->document()->hasAnyPrimaryResources()) {
            return $resources;
        }

        if ($response->document()->isSingleResourceDocument()) {
            $resources[] = $response->document()->primaryResource();
        } else {
            $resources = $response->document()->primaryResources();
        }

        if (!$response->document()->hasAnyIncludedResources()) {
            return $resources;
        }

        return array_merge($resources, $response->document()->includedResources());
    }
}
