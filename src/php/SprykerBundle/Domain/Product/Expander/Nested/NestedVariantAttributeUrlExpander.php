<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\ProductExpanderInterface;
use Frontastic\Common\SprykerBundle\Domain\Route\Service\VariantUrlEncoder;

class NestedVariantAttributeUrlExpander implements ProductExpanderInterface
{
    private const FIELD = '_url';

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Route\Service\VariantUrlEncoder
     */
    private $urlEncoder;

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\Route\Service\VariantUrlEncoder $urlEncoder
     */
    public function __construct(VariantUrlEncoder $urlEncoder)
    {
        $this->urlEncoder = $urlEncoder;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param array|\WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includes
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product
     */
    public function expand(Product $product, array $includes): Product
    {
        foreach ($product->variants as $variant) {
            $variant->attributes[self::FIELD] = $this->urlEncoder->encode($product, $variant);
        }

        return $product;
    }
}
