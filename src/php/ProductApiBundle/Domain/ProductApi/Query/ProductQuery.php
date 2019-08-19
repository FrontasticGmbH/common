<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

class ProductQuery extends Query
{
    public const SORT_ORDER_ASCENDING = 'ascending';
    public const SORT_ORDER_DESCENDING = 'descending';

    /**
     * @var string
     */
    public $category;

    /**
     * @var string
     */
    public $sku;

    /**
     * @var array
     */
    public $skus;

    /**
     * @var string
     */
    public $productId;

    /**
     * @var array
     */
    public $productIds;

    /**
     * @var string
     */
    public $productType;

    /**
     * @TODO: Currency should only be stored in context. Property should be removed.
     *
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $query;

    /**
     * @stability trial This field might change to a more suffisticated structure
     * @var array map of field identifiers to string filter values
     */
    public $filter = [];

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet[]
     */
    public $facets = [];

    /**
     * Map of sort attributes => sort order
     *
     * @var mixed
     */
    public $sortAttributes = [];

    /**
     * @return void
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\InvalidQueryException
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
    }
}
