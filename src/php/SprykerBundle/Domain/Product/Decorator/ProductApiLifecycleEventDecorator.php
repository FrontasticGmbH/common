<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Decorator;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\LifecycleEventDecorator;

class ProductApiLifecycleEventDecorator extends LifecycleEventDecorator
{
    public function getProductConcrete(string $sku)
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
