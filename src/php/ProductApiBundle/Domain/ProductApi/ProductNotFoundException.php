<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

class ProductNotFoundException extends Exception
{
    public static function byProperty(string $propertyName, string $value): ProductNotFoundException
    {
        return new static(sprintf('Product with %s "%s" not found', $propertyName, $value));
    }

    public static function byProductId(string $productId): ProductNotFoundException
    {
        return static::byProperty('product ID', $productId);
    }

    public static function bySku(string $sku): ProductNotFoundException
    {
        return static::byProperty('SKU', $sku);
    }
}
