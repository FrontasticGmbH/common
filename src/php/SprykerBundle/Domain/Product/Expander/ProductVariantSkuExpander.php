<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiExtendedConstants;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

/**
 * Expands the product variants, replacing id, sku and groupId.
 * Requires the included resource 'concrete-products'
 */
class ProductVariantSkuExpander implements ProductExpanderInterface
{
    use IncludedResourceExpanderTrait;

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product|null $product
     * @param array|\WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includes
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product|null
     */
    public function expand(Product $product, array $includes): Product
    {
        $concreteProduct = $this->getResourceIncludeByAttributeKey(
            $includes,
            SprykerProductApiExtendedConstants::PRODUCT_RESOURCE_CONCRETE_PRODUCTS,
            'productAbstractSku',
            $this->getProductId($product)
        );

        if (!$concreteProduct) {
            return $product;
        }

        $this->expandVariants($product->variants, $concreteProduct);

        return $product;
    }

    protected function getResourceIncludeByProductId(array $includes, string $type, string $resourceId): ?ResourceObject
    {
        foreach ($includes as $include) {
            if ($include->type() === $type && $include->attributes()['productAbstractSku'] === $resourceId) {
                return $include;
            }
        }

        return null;
    }

    protected function getProductId(Product $product): string
    {
        return $product->productId;
    }

    /**
     * @param array|\Frontastic\Common\ProductApiBundle\Domain\Variant[] $variants
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return void
     */
    protected function expandVariants(array $variants, ResourceObject $resource): void
    {
        $variants[0]->id = $resource->id();
        $variants[0]->sku = $resource->attributes()['sku'];
        $variants[0]->groupId = $resource->attributes()['sku'];
    }
}
