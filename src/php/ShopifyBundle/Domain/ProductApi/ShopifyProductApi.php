<?php

namespace Frontastic\Common\ShopifyBundle\Domain\ProductApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;
use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopifyBundle\Domain\ResponseAccess;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;
use GuzzleHttp\Promise\PromiseInterface;

class ShopifyProductApi implements ProductApi
{
    /**
     * @var ShopifyClient
     */
    private $client;

    public function __construct(ShopifyClient $client)
    {
        $this->client = $client;
    }

    public function getCategories(CategoryQuery $query): array
    {
        // TODO: Implement getCategories() method.
    }

    public function getProductTypes(ProductTypeQuery $query): array
    {
        // TODO: Implement getProductTypes() method.
    }

    public function getProduct($query, string $mode = self::QUERY_SYNC): ?object
    {
        // TODO: Implement getProduct() method.
    }

    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        $query->query = "{
          products(first: $query->limit) {
            edges {
              cursor
              node {
                id
                title
                description
                handle
                updatedAt
                collections(first: 10) {
                  edges {
                    node {
                      id
                    }
                  }
                }
                variants(first: 1) {
                  edges {
                    node {
                      id
                      sku
                      title
                      currentlyNotInStock
                      priceV2 {
                        amount
                        currencyCode
                      }
                      product {
                        id
                      }
                      selectedOptions {
                        name
                        value
                      }
                      image {
                        originalSrc
                      }
                    }
                  }
                }
              }
            }
            pageInfo {
              hasNextPage
              hasPreviousPage
            }
          }
        }";

        $promise = $this->client
            ->request($query->query, $query->locale)
            ->then(function ($result) use ($query): ProductApi\Result {
                $resultBody = $result['body'];
                $cursor = null;

                foreach ($resultBody['data']['products']['edges'] as $product) {
                    $products[] = $this->mapDataToProduct($product['node']);
                    $cursor = $product['cursor'];
                }

                return new ProductApi\Result([
                    // @TODO: "offset" and "total" are not available in Shopify. They implement cursor-based pagination
                    'cursor' => $cursor,
                    'hasNextPage' => $resultBody['data']['products']['pageInfo']['hasNextPage'],
                    'hasPreviousPage' => $resultBody['data']['products']['pageInfo']['hasPreviousPage'],
                    'count' => count($products),
                    'items' => $products,
                    'query' => clone $query,
                ]);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;

    }

    public function getDangerousInnerClient(): ShopifyClient
    {
        return $this->client;
    }

    private function mapDataToProduct(array $productData): Product
    {
        return new Product([
            'productId' => $productData['id'],
            'name' => $productData['title'],
            'description' => $productData['description'],
            'slug' => $productData['handle'],
            'categories' => array_map(
                function (array $category) {
                    return $category['node']['id'];
                },
                $productData['collections']['edges']
            ),
            'changed' => $this->parseDate($productData['updatedAt']),
            'variants' => $this->mapDataToVariants($productData['variants']['edges']),
            // @TODO Include dangerousInnerVariant base on locale flag
            // 'dangerousInnerProduct' => $productData,
        ]);
    }

    private function parseDate(string $string): \DateTimeImmutable
    {
        $formats = [
            'Y-m-d\TH:i:s.uP',
            \DateTimeInterface::RFC3339,
            \DateTimeInterface::RFC3339_EXTENDED,
        ];

        foreach ($formats as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $string);
            if ($date !== false) {
                return $date;
            }
        }

        throw new \RuntimeException('Invalid date: ' . $string);
    }

    private function mapDataToVariants(array $variantsData): array
    {
        $variants = [];
        foreach ($variantsData as $variant) {
            $variants[] = $this->mapDataToVariant($variant['node']);
        }

        return $variants;
    }

    private function mapDataToVariant(array $variantData): Variant
    {
        return new Variant([
            'id' => $variantData['id'],
            'sku' => $variantData['sku'],
            'groupId' => $variantData['product']['id'],
            'isOnStock' => !$variantData['currentlyNotInStock'],
            'price' => $this->mapDataToPriceValue($variantData['priceV2']),
            'currency' => $variantData['priceV2']['currencyCode'],
            'attributes' => $this->mapDataToAttributes($variantData),
            'images' =>  [$variantData['image']['originalSrc']],
            // @TODO Include dangerousInnerVariant base on locale flag
            // 'dangerousInnerVariant' => $variantData,
        ]);
    }

    private function mapDataToPriceValue(array $data): int
    {
        return (int)round($data['amount'] * 100);
    }

    private function mapDataToAttributes(array $variantData): array
    {
        return array_combine(
            array_map(
                function (array $attribute): string {
                    return $attribute['name'];
                },
                $variantData['selectedOptions']
            ),
            array_map(
                function (array $attribute) {
                    return $attribute['value'];
                },
                $variantData['selectedOptions']
            )
        );
    }
}
