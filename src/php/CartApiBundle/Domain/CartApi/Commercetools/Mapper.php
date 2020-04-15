<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;

class Mapper
{
    public function dataToShippingMethod(array $shipping): ?ShippingMethod
    {
        if (!count($shipping)) {
            return null;
        }

        return new ShippingMethod([
            'name' => $shipping['shippingMethodName'] ?? null,
            'price' => $shipping['price']['centAmount'] ?? null,
        ]);
    }
}
