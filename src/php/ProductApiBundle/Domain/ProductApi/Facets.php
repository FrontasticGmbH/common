<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

interface Facets
{
    /**
     * Available facet types.
     */
    const TYPE_RANGE = 'range',
          TYPE_TERM = 'term';

    const KEY_PRICE_RANGE = 'price_range',
          KEY_SIZE_RANGE = 'size_range',
          KEY_CATEGORY_TERMS = 'category_terms',
          KEY_CATEGORY_TREE_TERMS = 'category_tree_terms',
          KEY_COLOR_TERMS = 'color_terms',
          KEY_CUSTOM_TERMS = 'custom_terms',
          KEY_MATERIAL_TERMS = 'material_terms',
          KEY_TARGET_AUDIENCE_TERMS = 'target_audience_terms';
}
