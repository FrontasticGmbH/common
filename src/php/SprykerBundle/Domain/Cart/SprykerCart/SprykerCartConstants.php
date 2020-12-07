<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart;

interface SprykerCartConstants
{
    public const COMMON_CART_RELATIONSHIPS = [
        'abstract-product-image-sets',
        'abstract-product-prices',
        'abstract-product-availabilities',
        'concrete-products',
        'concrete-product-image-sets',
        'concrete-product-prices',
        'concrete-product-availabilities',
        'cart-rules',
        'vouchers',
    ];

    public const CUSTOMER_CART_RELATIONSHIPS = [
        'items',
    ];

    public const GUEST_CART_RELATIONSHIPS = [
        'guest-cart-items',
    ];
}
