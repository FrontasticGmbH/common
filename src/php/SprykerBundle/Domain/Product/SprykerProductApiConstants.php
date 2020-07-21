<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

interface SprykerProductApiConstants
{
    public const PRODUCT_RESOURCE_PRODUCT_ABSTRACT = 'abstract-products';

    public const PRODUCT_RESOURCE_ABSTRACT_IMAGE_SETS = 'abstract-product-image-sets';

    public const SPRYKER_DEFAULT_PRODUCT_RESOURCES = [
        self::PRODUCT_RESOURCE_ABSTRACT_IMAGE_SETS,
        'abstract-product-prices',
        'abstract-product-availabilities',
        'concrete-products',
        'concrete-product-image-sets',
        'concrete-product-prices',
        'concrete-product-availabilities',
    ];

    public const SPRYKER_DEFAULT_CONCRETE_PRODUCT_RESOURCES = [
        'concrete-product-image-sets',
        'concrete-product-prices',
        'concrete-product-availabilities',
    ];

    public const SPRYKER_PRODUCT_QUERY_RESOURCES = [
        self::PRODUCT_RESOURCE_PRODUCT_ABSTRACT,
    ];

    public const PRICE_WITH_DISCOUNT = 'DEFAULT';
    public const PRICE_OLD = 'ORIGINAL';
}
