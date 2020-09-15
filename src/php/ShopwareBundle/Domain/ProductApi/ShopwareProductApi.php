<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiBase;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Exception\RequestException;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\CategoryMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductResultMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Query\QueryFacetExpander;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchCriteriaBuilder;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface;
use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\Promise\promise_for;

class ShopwareProductApi extends ProductApiBase
{
    /** @var ClientInterface */
    private $client;

    /** @var LocaleCreator */
    private $localeCreator;

    /** @var DataMapperResolver */
    private $mapperResolver;

    /** @var EnabledFacetService */
    private $enabledFacetService;

    /** @var ShopwareProjectConfigApiInterface */
    private $projectConfigApi;

    /** @var string|null */
    private $defaultLanguage;

    public function __construct(
        ClientInterface $client,
        LocaleCreator $localeCreator,
        DataMapperResolver $mapperResolver,
        EnabledFacetService $enabledFacetService,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory,
        ProductSearchApi $productSearchApi,
        ?string $defaultLanguage
    ) {
        parent::__construct($productSearchApi);

        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->mapperResolver = $mapperResolver;
        $this->enabledFacetService = $enabledFacetService;
        $this->projectConfigApi = $projectConfigApiFactory->factor($this->client);
        $this->defaultLanguage = $defaultLanguage;
    }

    protected function queryCategoriesImplementation(CategoryQuery $query): Result
    {
        $criteria = SearchCriteriaBuilder::buildFromCategoryQuery($query);
        $locale = $this->parseLocaleString($query->locale);
        $mapper = $this->buildMapper(CategoryMapper::MAPPER_NAME, $locale, $query);

        $categories = $this->client
            ->forLanguage($locale->languageId)
            ->post('/sales-channel-api/v2/category', [], $criteria)
            ->then(function ($response) use ($mapper) {
                return $mapper->map($response);
            })
            ->wait();

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
        $client = $this->client
            ->forCurrency($locale->currencyId)
            ->forLanguage($locale->languageId);
        $mapper = $this->buildMapper(ProductMapper::MAPPER_NAME, $locale, $query);

        if ($query->productId !== null) {
            $productIdPromise = promise_for($query->productId);
        } elseif ($query->sku !== null) {
            $criteria = SearchCriteriaBuilder::buildFromSimpleProductQuery($query);

            $productIdPromise = $client
                ->post('/sales-channel-api/v2/product', [], $criteria)
                ->then(function ($response) use ($query): string {
                    $product = $response['data'][0] ?? [];
                    $productId = $product['parentId'] ?? $product['id'] ?? null;
                    if ($productId === null) {
                        throw ProductApi\ProductNotFoundException::fromQuery($query);
                    }
                    return $productId;
                });
        } else {
            throw new \RuntimeException('Not implemented');
        }

        return $productIdPromise
            ->then(function (string $productId) use ($mapper, $query, $client): PromiseInterface {
                return $client
                    ->get('/sales-channel-api/v2/product/' . $productId, ['associations[children][]' => 1])
                    ->then(function ($response) use ($mapper, $query): Product {
                        $product = $mapper->map($response);
                        if ($product === null) {
                            throw ProductApi\ProductNotFoundException::fromQuery($query);
                        }
                        return $product;
                    });
            })
            ->otherwise(function (\Throwable $exception) use ($query) {
                if ($exception instanceof RequestException) {
                    $messagePrefix = 'Value is not a valid UUID:';
                    if ($exception->getCode() === 400 &&
                        substr($exception->getMessage(), 0, strlen($messagePrefix)) === $messagePrefix) {
                        throw ProductApi\ProductNotFoundException::fromQuery($query);
                    }
                }

                throw $exception;
            });
    }

    public function getDangerousInnerClient(): ClientInterface
    {
        return $this->client;
    }

    private function parseLocaleString(string $localeString): ShopwareLocale
    {
        return $this->localeCreator->createLocaleFromString($localeString ?? $this->defaultLanguage);
    }

    private function buildMapper(
        string $mapperName,
        ShopwareLocale $locale,
        ProductApi\Query $query
    ): DataMapperInterface {
        $mapper = $this->mapperResolver->getMapper($mapperName);
        if ($mapper instanceof LocaleAwareDataMapperInterface) {
            $mapper->setLocale($locale);
        }
        if ($mapper instanceof ProjectConfigApiAwareDataMapperInterface) {
            $mapper->setProjectConfigApi($this->projectConfigApi);
        }
        if ($mapper instanceof QueryAwareDataMapperInterface) {
            $mapper->setQuery($query);
        }
        return $mapper;
    }
}
