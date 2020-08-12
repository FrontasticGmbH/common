<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

interface SprykerProductApiExtendedConstants extends SprykerProductApiConstants
{
    public const PRODUCT_RESOURCE_SEARCH = 'catalog-search';
    public const PRODUCT_RESOURCE_EXTENDED_BRAND = 'brand';
    public const PRODUCT_RESOURCE_EXTENDED_MERCHANT = 'merchant';
    public const PRODUCT_RESOURCE_EXTENDED_BUNDLE_COMPONENTS = 'concrete-product-bundle-components';
    public const PRODUCT_RESOURCE_EXTENDED_SPECIAL_ICONS = 'product-icons';
    public const PRODUCT_RESOURCE_EXTENDED_LABELS = 'product-labels';
    public const PRODUCT_RESOURCE_CATEGORIES = 'category-nodes';
    public const PRODUCT_RESOURCE_ABSTRACT_AVAILABILITIES = 'abstract-product-availabilities';

    public const DEFAULT_IMAGE_SET_NAME = 'default';
    public const IMAGE_SIZE_LARGE = 'large';
    public const IMAGE_SIZE_MEDIUM = 'medium';
    public const IMAGE_SIZE_SMALL = 'small';

    public const SPECIAL_PRODUCT_TYPE_SEARCH_RESULT = 'search_result';

    // A meta-attribute containing all attributes of the abstract parent. Will only be added to the first variant
    public const META_ATTRIBUTE_ABSTRACT = '_attributes_abstract';
}
