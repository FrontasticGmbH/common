<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class SapDataMapper
{
    public const ANONYMOUS_USER_ID = 'anonymous';

    /** @var SapClient */
    private $client;

    public function __construct(SapClient $client)
    {
        $this->client = $client;
    }

    public function mapDataToProduct(array $data): Product
    {
        $code = $data['code'];

        return new Product([
            'productId' => $code,
            'name' => $data['name'] ?? $code,
            'slug' => rawurlencode($code),
            'description' => $data['description'] ?? '',
            'categories' => array_map(
                function (array $category): string {
                    return $category['code'];
                },
                $data['categories'] ?? []
            ),
            'variants' => [
                new Variant([
                    'id' => $code,
                    'sku' => $code, /// @FIXME get the real SKU
                    'groupId' => $code,
                    'price' => $this->mapDataToPriceValue($data['price']),
                    'currency' => $data['price']['currencyIso'],
                    'images' => array_values(
                        array_map(
                            function (array $image): string {
                                return $this->client->getHostUrl() . $image['url'];
                            },
                            array_filter(
                                $data['images'] ?? [],
                                function (array $image): bool {
                                    return $image['format'] !== 'thumbnail';
                                }
                            )
                        )
                    ),
                ]),
            ],
        ]);
    }

    /**
     * @return Category[]
     */
    public function mapDataToCategories(array $data, string $parentPath = '', int $depth = 0): array
    {
        $categoryId = $data['id'];
        $categoryPath = $parentPath . '/' . $categoryId;

        $categories = [
            new Category([
                'categoryId' => $categoryId,
                'name' => $data['name'] ?? $categoryId,
                'depth' => $depth,
                'path' => $categoryPath,
                'slug' => rawurlencode($categoryId),
            ]),
        ];

        foreach ($data['subcategories'] ?? [] as $subcategoryData) {
            $categories = array_merge(
                $categories,
                $this->mapDataToCategories($subcategoryData, $categoryPath, $depth + 1)
            );
        }

        return $categories;
    }

    public function mapDataToCart(array $data, string $userId): Cart
    {
        if ($userId === self::ANONYMOUS_USER_ID) {
            $sapCartId = $data['guid'];
        } else {
            $sapCartId = $data['code'];
        }
        $cartId = sprintf('%s:%s', $userId, $sapCartId);

        return new Cart([
            'cartId' => $cartId,
            'cartVersion' => '1',
            'sum' => $this->mapDataToPriceValue($data['totalPrice']),
            'currency' => $data['totalPrice']['currencyIso'],
            'lineItems' => array_map(
                function (array $lineItemData): LineItem {
                    $product = $lineItemData['product'];

                    return new LineItem\Variant([
                        'lineItemId' => (string)$lineItemData['entryNumber'],
                        'name' => $product['name'] ?? $product['code'],
                        'count' => $lineItemData['quantity'],
                        'price' => $this->mapDataToPriceValue($lineItemData['basePrice']),
                        'totalPrice' => $this->mapDataToPriceValue($lineItemData['totalPrice']),
                        'currency' => $lineItemData['totalPrice']['currencyIso'],
                    ]);
                },
                $data['entries'] ?? []
            ),
        ]);
    }

    private function mapDataToPriceValue(array $data): int
    {
        return (int)round($data['value'] * 100);
    }
}
