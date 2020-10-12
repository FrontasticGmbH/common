<?php

namespace Frontastic\Common\ShopifyBundle\Domain\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class ShopifyProductMapper
{
    public function mapDataToProduct(array $productData, Query $query = null): Product
    {
        return new Product([
            'productId' => $productData['id'] ?? null,
            'name' => $productData['title'] ?? null,
            'description' => $productData['description'] ?? null,
            'slug' => $productData['handle'] ?? null,
            'categories' => array_map(
                function (array $category) {
                    return $category['node']['id'];
                },
                $productData['collections']['edges']
            ),
            'changed' => $this->parseDate($productData['updatedAt']),
            'variants' => $this->mapDataToVariants($productData['variants']['edges'], $query),
            'dangerousInnerProduct' => $this->dataToDangerousInnerData($productData, $query),
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

    public function dataToDangerousInnerData(array $rawData, Query $query = null): ?array
    {
        if (is_null($query)) {
            return null;
        }
        if ($query->loadDangerousInnerData) {
            return $rawData;
        }
        return null;
    }

    public function mapDataToVariants(array $variantsData, Query $query = null): array
    {
        $variants = [];
        foreach ($variantsData as $variant) {
            $variants[] = $this->mapDataToVariant($variant['node'], $query);
        }

        return $variants;
    }

    public function mapDataToVariant(array $variantData, Query $query = null): Variant
    {
        return new Variant([
            'id' => $variantData['id'] ?? null,
            'sku' => $variantData['sku'] ?? null,
            'groupId' => $variantData['product']['id'] ?? null,
            'isOnStock' => !$variantData['currentlyNotInStock'] ?? null,
            'price' => $this->mapDataToPriceValue($variantData['priceV2'] ?? []),
            'currency' => $variantData['priceV2']['currencyCode'] ?? null,
            'attributes' => $this->mapDataToAttributes($variantData),
            'images' => [$variantData['image']['originalSrc']] ?? null,
            'dangerousInnerVariant' => $this->dataToDangerousInnerData($variantData, $query),
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
