<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiExtendedConstants;

/**
 * Expands the basic product adding the description.
 * Requires the included resource 'abstract-products'
 */
class AbstractProductDescriptionExpander implements ProductExpanderInterface
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
        $resource = $this->getResourceInclude(
            $includes,
            SprykerProductApiExtendedConstants::PRODUCT_RESOURCE_PRODUCT_ABSTRACT,
            $product->productId
        );

        if (!$resource) {
            return $product;
        }

        $product->description = $resource->attribute('description');

        return $product;
    }
}
