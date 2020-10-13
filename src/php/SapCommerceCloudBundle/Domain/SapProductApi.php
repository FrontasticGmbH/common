<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiBase;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreator;
use GuzzleHttp\Promise\PromiseInterface;

class SapProductApi extends ProductApiBase
{
    /** @var SapClient */
    private $client;

    /** @var SapLocaleCreator */
    private $localeCreator;

    /** @var SapDataMapper */
    private $dataMapper;

    public function __construct(
        SapClient $client,
        SapLocaleCreator $localeCreator,
        SapDataMapper $dataMapper,
        ProductSearchApi $productSearchApi
    ) {
        parent::__construct($productSearchApi);

        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->dataMapper = $dataMapper;
    }

    protected function queryCategoriesImplementation(CategoryQuery $query): Result
    {
        $categories = $this->client
            ->get(
                '/rest/v2/{siteId}/catalogs/{catalogId}/{catalogVersionId}',
                array_merge(
                    $this->localeCreator->createLocaleFromString($query->locale)->toQueryParameters(),
                    [
                        'fields' => 'FULL',
                    ]
                )
            )
            ->then(function (array $data): array {
                $categories = [];

                foreach ($data['categories'] as $categoryData) {
                    $categories = array_merge(
                        $categories,
                        $this->dataMapper->mapDataToCategories($categoryData)
                    );
                }

                return array_values($categories);
            })
            ->then(function (array $categories) use ($query): array {
                if ($query->slug !== null) {
                    return array_filter(
                        $categories,
                        function (Category $category) use ($query) {
                            return $category->slug === $query->slug;
                        }
                    );
                }
                return $categories;
            })
            ->then(function (array $categories) use ($query): array {
                return array_slice(
                    $categories,
                    $query->offset,
                    $query->limit
                );
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
        return [
            new ProductType([
                'productTypeId' => 'product',
                'name' => 'Product',
            ]),
        ];
    }

    protected function getProductImplementation(SingleProductQuery $query): PromiseInterface
    {
        if ($query->productId !== null) {
            $code = $query->productId;
        } elseif ($query->sku !== null) {
            // Since we can't access the SKU (or EAN) in the API, we treat the SKU as another code.
            $code = $query->sku;
        } else {
            throw new ProductApi\Exception\InvalidQueryException('Query needs product ID or SKU');
        }

        return $this->client
            ->get(
                '/rest/v2/{siteId}/products/' . $code,
                array_merge(
                    $this->localeCreator->createLocaleFromString($query->locale)->toQueryParameters(),
                    [
                        'fields' => 'FULL',
                    ]
                )
            )
            ->then(function (array $data): Product {
                return $this->dataMapper->mapDataToProduct($data);
            })
            ->otherwise(function (\Throwable $exception) use ($query) {
                if ($exception instanceof SapRequestException && $exception->getCode() === 400) {
                    if ($query->sku !== null) {
                        throw ProductApi\ProductNotFoundException::bySku($query->sku);
                    }
                    throw ProductApi\ProductNotFoundException::byProductId($query->productId);
                }
                throw $exception;
            });
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
