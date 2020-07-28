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
          products(first: 3) {
            edges {
              node {
                id
                title
                variants(first: 1) {
                  edges {
                    node {
                      id
                      sku
                      title
                      priceV2 {
                        amount
                        currencyCode
                      }
                    }
                  }
                }
              }
            }
          }
        }";

        $promise = $this->client
            ->request($query->query, $query->locale)
            ->then(function ($result) use ($query): ProductApi\Result {
                foreach ($result['body']['data']['products']['edges'] as $product) {
                    $products[] = new Product([
                        'productId' => $product['node']['id'],
                        'name' => $product['node']['title']
                    ]);
                }

                return new ProductApi\Result([
//                    'offset' => $result['pagination']['currentPage'] * $result['pagination']['pageSize'],
//                    'total' => $result['pagination']['totalResults'],
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

    public function getDangerousInnerClient()
    {
        // TODO: Implement getDangerousInnerClient() method.
    }
}
