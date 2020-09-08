<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\ProductExpanderInterface;
use Frontastic\Common\SprykerBundle\Common\NestedAttributeValueTransformExpanderTrait;

/**
 * Expands the product attributes making them nested.
 * Requires to be the first expander registered.
 */
class NestedAttributeValueTransformExpander implements ProductExpanderInterface
{
    use NestedAttributeValueTransformExpanderTrait;

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product|null $product
     * @param array|\WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includes
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product|null
     */
    public function expand(Product $product, array $includes): Product
    {
        array_map([$this, 'expandVariant'], $product->variants);

        return $product;
    }
}
