<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiBase;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\SprykerBundle\BaseApi\ProductExpandingTrait;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\Locale\SprykerLocale;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested\NestedAttributeValueTransformExpander;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\CategoriesMapper;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\ProductConcreteMapper;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\ProductMapper;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\SprykerUrlAppender;
use GuzzleHttp\Promise\PromiseInterface;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

class SprykerProductApi extends ProductApiBase
{
    use ProductExpandingTrait;

    /** @var SprykerClientInterface */
    private $client;

    /** @var MapperResolver */
    private $mapperResolver;

    /** @var LocaleCreator */
    private $localeCreator;

    /** @var SprykerUrlAppender */
    private $urlAppender;

    /** @var array */
    protected $productResources;

    /** @var array */
    private $queryResources;

    /** @var array */
    protected $concreteProductResources;

    /** @var string */
    private $defaultLanguage;

    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        SprykerUrlAppender $urlAppender,
        ProductSearchApi $productSearchApi,
        string $defaultLanguage,
        array $productResources = SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_PRODUCT_RESOURCES,
        array $queryResources = SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES,
        array $concreteProductResources = SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_CONCRETE_PRODUCT_RESOURCES
    ) {
        parent::__construct($productSearchApi);

        $this->client = $client;
        $this->mapperResolver = $mapperResolver;
        $this->localeCreator = $localeCreator;
        $this->urlAppender = $urlAppender;
        $this->defaultLanguage = $defaultLanguage;
        $this->productResources = $productResources;
        $this->queryResources = $queryResources;
        $this->concreteProductResources = $concreteProductResources;
    }

    protected function queryCategoriesImplementation(CategoryQuery $query): Result
    {
        $locale = $this->parseLocaleString($query->locale);

        $response = $this->client
            ->forLanguage($locale->language)
            ->get('/category-trees');

        $document = $response->document();
        $categories = $this->mapperResolver->getMapper(CategoriesMapper::MAPPER_NAME)->mapResource(
            $document->isSingleResourceDocument() ?
                $document->primaryResource() :
                $document->primaryResources()[0]
        );

        return new Result([
            'count' => count($categories),
            'items' => $categories,
            'query' => clone($query),
        ]);
    }

    protected function getProductTypesImplementation(ProductTypeQuery $query): array
    {
        return [];
    }

    protected function getProductImplementation(SingleProductQuery $query): PromiseInterface
    {
        $locale = $this->parseLocaleString($query->locale);

        if ($query->sku) {
            $url = $this->urlAppender->withIncludes(
                "/concrete-products/{$query->sku}",
                $this->concreteProductResources
            );
            $mapper = $this->mapperResolver->getMapper(ProductConcreteMapper::MAPPER_NAME);
        } else {
            $url = $this->urlAppender->withIncludes(
                "/abstract-products/{$query->productId}",
                $this->productResources
            );
            $mapper = $this->mapperResolver->getMapper(ProductMapper::MAPPER_NAME);
            $this->registerProductExpander(new NestedAttributeValueTransformExpander());
        }

        return $this->client
            ->forLanguage($locale->language)
            ->get(
                $this->urlAppender->appendCurrencyToUrl($url, $locale->currency),
                [],
                ProductApi::QUERY_ASYNC
            )
            ->then(function ($response) use ($mapper) {
                $document = $response->document();
                $product = $mapper->mapResource(
                    $document->isSingleResourceDocument() ?
                        $document->primaryResource() :
                        $document->primaryResources()[0]
                );
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

    private function parseLocaleString(?string $localeString): SprykerLocale
    {
        return $this->localeCreator->createLocaleFromString($localeString ?? $this->defaultLanguage);
    }
}
