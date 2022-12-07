<?php

namespace Frontastic\Common\ShopifyBundle\Domain\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Filter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;

class ShopifyProductMapper
{
    public function mapDataToProduct(array $productData, Query $query = null): Product
    {
        return new Product([
            'productId' => ShopifyIdMapper::mapDataToId($productData['id'] ?? null),
            'name' => $productData['title'] ?? null,
            'description' => $productData['description'] ?? null,
            'slug' => $productData['handle'] ?? null,
            'categories' => array_map(
                function (array $category) {
                    return ShopifyIdMapper::mapDataToId($category['node']['id']);
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
            'id' => ShopifyIdMapper::mapDataToId($variantData['id'] ?? null),
            'sku' => $variantData['sku'] ?? null,
            'groupId' => ShopifyIdMapper::mapDataToId($variantData['product']['id'] ?? null),
            'isOnStock' => $variantData['quantityAvailable'] && $variantData['quantityAvailable'] > 0,
            'price' => $this->mapDataToPriceValue($variantData['priceV2'] ?? []),
            'currency' => $variantData['priceV2']['currencyCode'] ?? null,
            'attributes' => $this->mapDataToVariantAttributes($variantData),
            'images' => $this->mapDataToVariantImages($variantData),
            'dangerousInnerVariant' => $this->dataToDangerousInnerData($variantData, $query),
        ]);
    }

    public function mapDataToPriceValue(array $data): int
    {
        return (int)round($data['amount'] * 100);
    }

    public function mapDataToVariantAttributes(array $variantData): array
    {
        return array_combine(
            array_map(
                function (array $attribute): string {
                    return strtolower($attribute['name']);
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

    public function mapDataToFacets(array $productsData): array
    {
        $facets[] = $this->mapDataToTagFacet($productsData);
        $facets[] = $this->mapDataToProductTypeFacet($productsData);

        return array_filter($facets);
    }

    public function mapDataToProductAttributes(array $productAttributesData): array
    {
        $attributes = [];
        $productTags = [];

        foreach ($productAttributesData['productTags']['edges'] as $productTag) {
            if (empty($productTag['node'])) {
                continue;
            }
            $productTags[] = [
                'key' => $productTag['node'],
                'label' => $productTag['node'],
            ];
        }

        if (!empty($productTags)) {
            $attributeId = 'tag';
            $attributes[$attributeId] = new Attribute([
                'attributeId' => $attributeId,
                'type' => Attribute::TYPE_ENUM,
                'label' => null,
                'values' => $productTags,
            ]);
        }

        $attributeId = 'available_for_sale';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_BOOLEAN,
            'label' => null,
        ]);

        /** TODO: Attributes should include a data range filter */
        // $attributeId = 'created_at';
        // $attributes[$attributeId] = new Attribute([
        //    'attributeId' => $attributeId,
        //    'type' => Attribute::TYPE_TEXT,
        //    'label' => null,
        // ]);

        /** TODO: Attributes should include a data range filter */
        // $attributeId = 'updated_at';
        // $attributes[$attributeId] = new Attribute([
        //    'attributeId' => $attributeId,
        //    'type' => Attribute::TYPE_TEXT,
        //    'label' => null,
        //]);

        $attributeId = 'variants.price';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
            'label' => null,
        ]);

        $attributeId = 'vendor';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_TEXT,
            'label' => null,
        ]);

        $attributeId = 'categories.id';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_CATEGORY_ID,
            'label' => null, // Can we get the label somehow?
        ]);

        return $attributes;
    }

    public function toFilterString(Filter $queryFilter): string
    {
        switch ($queryFilter->attributeType) {
            case Attribute::TYPE_MONEY:
                $filterString = sprintf(
                    '%s:>=%s %s:<=%s',
                    $queryFilter->handle,
                    (float) $queryFilter->min / 100,
                    $queryFilter->handle,
                    (float) $queryFilter->max / 100
                );
                break;
            case Attribute::TYPE_BOOLEAN:
                $filterString = sprintf(
                    '%s:%s',
                    $queryFilter->handle,
                    $queryFilter->terms[0] ? 'true': 'false'
                );
                break;
            case Attribute::TYPE_ENUM:
                $filterString = sprintf(
                    '(%s:%s)',
                    $queryFilter->handle,
                    implode(" AND $queryFilter->handle:", $queryFilter->terms ?? [])
                );
                break;
            default:
                $filterString = sprintf(
                    '%s:*%s*',
                    $queryFilter->handle,
                    implode(" ", $queryFilter->terms ?? [])
                );
        }

        return $filterString;
    }

    private function mapDataToTagFacet(array $productsData): ?Facet
    {
        $productTagValues = [];
        foreach ($productsData as $productData) {
            $productData = $productData['node'] ?? $productData;
            if (!is_array($productData) || !key_exists('tags', $productData)) {
                continue;
            }

            foreach ($productData['tags'] as $tag) {
                $productTagValues[] = $tag;
            }
        }

        if (empty($productTagValues)) {
            return null;
        }

        $tagTerms = [];
        foreach (array_count_values($productTagValues) as $productTag => $count) {
            $tagTerms[] = new Term([
                'handle' => $productTag,
                'name' => $productTag,
                'value' => $productTag,
                'count' => $count,
            ]);
        }

        if (count($tagTerms)) {
            return new TermFacet([
                'handle' => 'tag',
                'key' => 'tag',
                'terms' => $tagTerms,
            ]);
        }

        return null;
    }

    private function mapDataToProductTypeFacet(array $productsData): ?Facet
    {
        $productTypeValues = [];
        foreach ($productsData as $productData) {
            $productData = $productData['node'] ?? $productData;
            if (!is_array($productData) || !key_exists('productType', $productData)) {
                continue;
            }

            $productTypeValues[] = $productData['productType'] ?? [];
        }

        if (empty($productTypeValues)) {
            return null;
        }

        $productTypeTerms = [];
        foreach (array_count_values($productTypeValues) as $productType => $count) {
            $productTypeTerms[] = new Term([
                'handle' => $productType,
                'name' => $productType,
                'value' => $productType,
                'count' => $count,
            ]);
        }

        if (count($productTypeTerms)) {
            return new TermFacet([
                'handle' => 'product_type',
                'key' => 'product_type',
                'terms' => $productTypeTerms,
            ]);
        }

        return null;
    }

    private function mapDataToVariantImages(array $variantData): array
    {
        $variantImages = [];

        if ($variantData['image'] !== null && key_exists('originalSrc', $variantData['image'])) {
            $variantImages = [$variantData['image']['originalSrc']] ?? [];
        }

        $productImages = array_map(
            function (array $image): string {
                return $image['node']['originalSrc'];
            },
            $variantData['product']['images']['edges']
        );

        return array_values(array_unique(array_merge($variantImages, $productImages)));
    }
}
