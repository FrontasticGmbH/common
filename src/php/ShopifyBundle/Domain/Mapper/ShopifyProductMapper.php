<?php

namespace Frontastic\Common\ShopifyBundle\Domain\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class ShopifyProductMapper
{
    public function mapDataToProduct(array $productData): Product
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
            // @TODO Include dangerousInnerProduct base on locale flag
            // 'dangerousInnerProduct' => $productData,
        ]);
    }

    public function parseDate(string $string): \DateTimeImmutable
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

    public function mapDataToVariants(array $variantsData): array
    {
        $variants = [];
        foreach ($variantsData as $variant) {
            $variants[] = $this->mapDataToVariant($variant['node']);
        }

        return $variants;
    }

    public function mapDataToVariant(array $variantData): Variant
    {
        return new Variant([
            'id' => $variantData['id'],
            'sku' => !empty($variantData['sku'])
                ? $variantData['sku']
                : $variantData['id'],
            'groupId' => $variantData['product']['id'],
            'isOnStock' => !$variantData['currentlyNotInStock'],
            'price' => $this->mapDataToPriceValue($variantData['priceV2']),
            'currency' => $variantData['priceV2']['currencyCode'],
            'attributes' => $this->mapDataToAttributes($variantData),
            'images' => [$variantData['image']['originalSrc']],
            // @TODO Include dangerousInnerVariant base on locale flag
            // 'dangerousInnerVariant' => $variantData,
        ]);
    }

    public function mapDataToPriceValue(array $data): int
    {
        return (int)round($data['amount'] * 100);
    }

    public function mapDataToAttributes(array $variantData): array
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
