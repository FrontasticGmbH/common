<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Expander;

use Frontastic\Common\ProductApiBundle\Domain\Product;

interface ProductExpanderInterface
{
    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param array $includes
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product
     */
    public function expand(Product $product, array $includes): Product;
}
