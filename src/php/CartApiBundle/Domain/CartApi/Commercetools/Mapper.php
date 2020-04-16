<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;

class Mapper
{
    public function mapDataToDiscounts(array $cart): array
    {
        if (empty($cart['discountCodes'])) {
            return [];
        }

        $discounts = [];
        foreach ($cart['discountCodes'] as $discount) {
            // Get the state from the $discount and save it in $discountCodeState variable
            // before assigning $discount['discountCode'] to $discount.
            $discountCodeState = $discount['state'] ?? null;
            $discount = $discount['discountCode'] ?? [];
            $discount = isset($discount['obj']) ? $discount['obj'] : $discount;
            $discounts[] = new Discount([
                'discountId' => $discount['id'] ?? 'undefined',
                'name' => $discount['name'] ?? null,
                'code' => $discount['code'] ?? null,
                'description' => $discount['description'] ?? null,
                'state' => $discountCodeState,
                'dangerousInnerDiscount' => $discount,
            ]);
        }

        return $discounts;
    }

    public function mapDataToShippingMethod(array $shipping): ?ShippingMethod
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
