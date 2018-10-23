<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

class ProductQuery extends Query
{
    /**
     * @var string
     */
    public $category;

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
    }
}
