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
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet[]
     */
    public $facets = [];

    /**
     * @var string
     */
    public $sortAttributeId;

    /**
     * @var string
     */
    public $sortOrder = self::SORT_ORDER_ASCENDING;

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
        $this->validateProperty('sortAttributeId', 'string');
        $this->validateProperty('sortOrder', 'string');
    }

    public function sortAscending(): bool
    {
        /*
         * ascending is the default so we interpret all non-descending values as ascending
         */

        return $this->sortOrder !== self::SORT_ORDER_DESCENDING;
    }

    public function sortDescending(): bool
    {
        return !$this->sortAscending();
    }
}
