<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\InvalidQueryException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;

/**
 * @type
 */
class ProductQuery extends PaginatedQuery
{
    public const SORT_ORDER_ASCENDING = 'ascending';
    public const SORT_ORDER_DESCENDING = 'descending';

    /**
     * @var ?string
     */
    public $category;

    /**
     * @var ?string[]
     */
    public $categories;

    /**
     * @deprecated use `skus` instead
     * @var ?string
     */
    public $sku;

    /**
     * @var ?string[]
     */
    public $skus;

    /**
     * @deprecated use `productIds` instead
     * @var ?string
     */
    public $productId;

    /**
     * @var ?string[]
     */
    public $productIds;

    /**
     * @var ?string
     */
    public $productType;

    /**
     * This is a full text search on the API
     *
     * @var ?string
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
     * All APIs support {@link ProductQuery::SORT_ORDER_ASCENDING} and {@link ProductQuery::SORT_ORDER_DESCENDING}.
     * Dedicated APIs might support additional values. If you use one of those, your code will not be portable to
     * other API implementations anymore (project specific).
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

    public function getAllUniqueCategories(): array
    {
        $categories = [];
        if ($this->category) {
            $categories[] = $this->category;
        }
        if ($this->categories) {
            $categories= array_merge($categories, $this->categories);
        }

        return array_unique($categories);
    }
}
