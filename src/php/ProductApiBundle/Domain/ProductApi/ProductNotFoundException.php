<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;

class ProductNotFoundException extends Exception
{
    public static function byProperty(string $propertyName, string $value): ProductNotFoundException
    {
        return new self(sprintf('Product with %s "%s" not found', $propertyName, $value));
    }

    public static function byProductId(string $productId): ProductNotFoundException
    {
        return static::byProperty('product ID', $productId);
    }

    public static function bySku(string $sku): ProductNotFoundException
    {
        return static::byProperty('SKU', $sku);
    }

    public static function fromQuery(SingleProductQuery $query): ProductNotFoundException
    {
        if ($query->productId !== null) {
            return static::byProductId($query->productId);
        }
        if ($query->sku !== null) {
            return static::bySku($query->sku);
        }

        return new self('Product not found');
    }
}
