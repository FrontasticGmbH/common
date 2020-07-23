<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\ProductExpanderInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\IncludedResourceExpanderTrait;
use Frontastic\Common\SprykerBundle\Domain\Product\Helper\VariantSuperAttributeResolver;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiExtendedConstants;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

/**
 * Expander does the following:
 *  * maps missing attributes from the super attribute map.
 *  * updates the product meta-type to the actual type
 *  * updates the first variant data to be the one that was searched for instead of a random first one (for search only)
 *  * updates the image of the first variant, if it was moved
 *
 * Requires the included resource 'abstract-products'
 * Optionally requires the included resource 'catalog-search'
 */
class NestedSearchProductAbstractExpander implements ProductExpanderInterface
{
    use IncludedResourceExpanderTrait;

    private const ATTRIBUTE_VARIANT_NAME = 'article_single_page_title';
    private const ATTRIBUTE_SUPER = '_super';
    private const FIELD_LABEL = 'label';
    private const FIELD_VALUE = 'value';
    private const PRODUCT_TYPE_SEARCH_RESULT = SprykerProductApiExtendedConstants::SPECIAL_PRODUCT_TYPE_SEARCH_RESULT;

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includes
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product
     */
    public function expand(Product $product, array $includes): Product
    {
        $productType = $product->version;
        $resource = $this->getResourceInclude(
            $includes,
            SprykerProductApiExtendedConstants::PRODUCT_RESOURCE_PRODUCT_ABSTRACT,
            $product->productId
        );

        if (!$resource) {
            return $product;
        }

        $product->version = $resource->attribute('productType');
        $this->mapVariantData($product, $resource);

        if ($productType === self::PRODUCT_TYPE_SEARCH_RESULT) {
            $searchResource = $this->getResourceIncludeByType(
                $includes,
                SprykerProductApiExtendedConstants::PRODUCT_RESOURCE_SEARCH
            );

            if ($searchResource) {
                $this->moveAndUpdateTheMainSearchVariant($product, $searchResource);
            } else {
                $this->leaveOnlyTheFirstVariant($product, 0);
            }
        }

        return $product;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return void
     */
    private function mapVariantData(Product $product, ResourceObject $resource): void
    {
        $commonAttributes = $this->formatCommonAttributes($resource);
        $variantAttributeMap = $resource->attribute('attributeMap', [
            'product_concrete_ids' => [],
            'super_attributes' => [],
            'attribute_variants' => [],
        ]);

        foreach ($variantAttributeMap['product_concrete_ids'] as $index => $sku) {
            $variant = $this->getOrCreateVariant($product, $index);
            $variant->sku = (string)$sku;
            $variant->id = $sku;
            $variant->groupId = $sku;
            $variant->attributes = $this->formatVariantAttributes(
                (string)$sku,
                $variant->attributes,
                $commonAttributes,
                $variantAttributeMap
            );

            $product->variants[$index] = $variant;
        }
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param int $index
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Variant
     */
    private function getOrCreateVariant(Product $product, int $index): Variant
    {
        if (isset($product->variants[$index])) {
            return $product->variants[$index];
        }

        if (isset($product->variants[0])) {
            return clone $product->variants[0];
        }

        return new Variant();
    }

    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return array
     */
    private function formatCommonAttributes(ResourceObject $resource): array
    {
        $attributes = [];
        $commonAttributes = $resource->attribute('attributes', []);
        $attributeLabels = $resource->attribute('attributeNames', []);
        $attributes[self::ATTRIBUTE_SUPER] = array_keys($resource->attribute('superAttributes', []));

        foreach ($commonAttributes as $key => $value) {
            $this->updateAttributeField($attributes, $key, self::FIELD_VALUE, $value);
        }

        foreach ($attributeLabels as $key => $label) {
            $this->updateAttributeField($attributes, $key, self::FIELD_LABEL, $label);
        }

        return $attributes;
    }

    /**
     * @param string $sku
     * @param array $attributes
     * @param array $commonAttributes
     * @param array $variantAttributeMap
     *
     * @return array
     */
    private function formatVariantAttributes(
        string $sku,
        array $attributes,
        array $commonAttributes,
        array $variantAttributeMap): array
    {
        $super = array_flip($attributes['_super'] ?? []);

        foreach ($commonAttributes as $key => $value) {
            if (strpos($key, '_') === 0) {
                $attributes[$key] = $value;
                continue;
            }

            $this->updateAttributeFieldFromArray($attributes, $key, self::FIELD_LABEL, $value);

            if (isset($attributes[$key][self::FIELD_VALUE], $super[$key])) {
                continue;
            }

            $this->updateAttributeFieldFromArray($attributes, $key, self::FIELD_VALUE, $value);
        }

        foreach (VariantSuperAttributeResolver::resolveMapAttributes($sku, $variantAttributeMap) as $key => $value) {
            if (isset($attributes[$key][self::FIELD_VALUE], $super[$key])) {
                continue;
            }

            $this->updateAttributeField($attributes, $key, self::FIELD_VALUE, $value, true);
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @param string $attributeKey
     * @param string $key
     * @param array|null $value
     *
     * @return void
     */
    private function updateAttributeFieldFromArray(
        array &$attributes,
        string $attributeKey,
        string $key,
        ?array $value
    ): void {
        $valueScalar = $value[$key] ?? null;
        $this->updateAttributeField($attributes, $attributeKey, $key, $valueScalar);
    }

    /**
     * @param array $attributes
     * @param string $key
     * @param string $attributeKey
     * @param mixed|null $value
     * @param bool $overwrite
     *
     * @return void
     */
    private function updateAttributeField(
        array &$attributes,
        string $attributeKey,
        string $key,
        $value,
        $overwrite = false
    ): void {
        $existing = $attributes[$attributeKey] ?? [];

        if (!is_array($existing)) {
            return;
        }

        if (isset($value) && ($overwrite || !isset($existing[$key]))) {
            $attributes[$attributeKey][$key] = $value;
        }
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return void
     */
    private function moveAndUpdateTheMainSearchVariant(Product $product, ResourceObject $resource): void
    {
        $productData = $this->findProductSearchData($product, $resource);

        if (!$productData) {
            return;
        }

        $variantIndex = $this->findVariant($product->variants, $productData['sku']);

        if ($variantIndex > 0) {
            $this->leaveOnlyTheFirstVariant($product, $variantIndex);
        }

        $this->updateFirstVariantWithProductData($product, $productData);
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return array
     */
    private function findProductSearchData(Product $product, ResourceObject $resource): array
    {
        foreach ($resource->attribute('abstractProducts', []) as $item) {
            if ($product->productId === $item['abstractSku']) {
                return $item['concreteProduct'] ?? [];
            }
        }

        return [];
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant[] $variants
     * @param string $sku
     *
     * @return int
     */
    private function findVariant(array $variants, string $sku): int
    {
        foreach ($variants as $index => $variant) {
            if ($variant->sku === $sku) {
                return $index;
            }
        }

        return 0;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param int $variantIndex
     *
     * @return void
     */
    private function leaveOnlyTheFirstVariant(Product $product, int $variantIndex): void
    {
        $product->variants = [$product->variants[$variantIndex]];
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param array $productData
     *
     * @return void
     */
    private function updateFirstVariantWithProductData(Product $product, array $productData): void
    {
        $firstVariant = $product->variants[0];

        foreach ($productData['attributes'] ?? [] as $key => $value) {
            $firstVariant->attributes[$key]['value'] = $value;
        }

        $firstVariant->attributes[self::ATTRIBUTE_VARIANT_NAME] = $product->name;

        if (isset($productData['externalUrlSmall'])) {
            $this->updateTheImage($firstVariant, $productData['externalUrlSmall']);
        }
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     * @param string $imageUrl
     *
     * @return void
     */
    private function updateTheImage(Variant $variant, string $imageUrl): void
    {
        foreach ($variant->images as $type => $sizes) {
            foreach ($sizes as $size => $images) {
                if (!is_array($images)) {
                    $variant->images[$type][$size] = [$images];
                }

                array_unshift($variant->images[$type][$size], $imageUrl);
            }
        }
    }
}
