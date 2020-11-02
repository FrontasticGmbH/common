<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiExtendedConstants;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

/**
 * Expands the product variants, replacing id, sku and groupId.
 * Requires the included resource 'concrete-products'
 */
class ProductVariantSkuExpander implements ProductExpanderInterface
{
    use IncludedResourceExpanderTrait;

    public function expand(Product $product, array $includes): Product
    {
        $concreteProduct = $this->getResourceIncludeByAttributeKey(
            $includes,
            SprykerProductApiExtendedConstants::PRODUCT_RESOURCE_CONCRETE_PRODUCTS,
            'productAbstractSku',
            $product->productId
        );

        if (!$concreteProduct) {
            return $product;
        }

        $this->expandVariant($product->variants[0], $concreteProduct);

        return $product;
    }

    protected function expandVariant(Variant $variant, ResourceObject $resource): void
    {
        $variant->id = $resource->id();
        $variant->sku = $resource->attributes()['sku'];
        $variant->groupId = $resource->attributes()['sku'];
    }
}
