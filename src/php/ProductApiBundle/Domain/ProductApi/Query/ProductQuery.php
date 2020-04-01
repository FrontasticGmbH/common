<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\InvalidQueryException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;

class ProductQuery extends PaginatedQuery
{
    public const SORT_ORDER_ASCENDING = 'ascending';
    public const SORT_ORDER_DESCENDING = 'descending';

    /**
     * @var string|null
     */
    public $category;

    /**
     * @deprecated use `skus` instead
     * @var string|null
     */
    public $sku;

    /**
     * @var string[]|null
     */
    public $skus;

    /**
     * @deprecated use `productIds` instead
     * @var string|null
     */
    public $productId;

    /**
     * @var string[]|null
     */
    public $productIds;

    /**
     * @var string|null
     */
    public $productType;

    /**
     * This is a full text search on the API
     *
     * @var string|null
     */
    public $query;

    /**
     * Filters that will be applied *before* the actual facets.  CommerceTools
     * allowed a list of filter strings, too, but this is deprecated in
     * commercetools.
     *
     * @stability experimental This field might change to a more sophisticate structure
     *
     * @var Filter[]
     */
    public $filter = [];

    /**
     * @var Facet[]
     */
    public $facets = [];

    /**
     * Map of sort attributes => sort order
     *
     * @var string[]
     */
    public $sortAttributes = [];

    /**
     * @var bool
     */
    public $fuzzy = false;

    /**
     * @throws InvalidQueryException
     */
    public function validate(): void
    {
        $this->validateProperty('category', 'string');
        $this->validateProperty('productId', 'string');
        $this->validateProperty('productIds', 'array');
        $this->validateProperty('productType', 'string');
        $this->validateProperty('currency', 'string');
        $this->validateProperty('query', 'string');
        $this->validateProperty('facets', 'array');
        $this->validateProperty('sortAttributes', 'array');
        $this->validateProperty('fuzzy', 'boolean');
    }
}
