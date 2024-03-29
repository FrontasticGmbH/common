<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;

/**
 * @type
 */
class CategoryQuery extends PaginatedQuery
{
    /**
     * @var string
     */
    public $parentId;

    /**
     * @var string
     */
    public $categoryId;

    /**
     * @var string
     */
    public $slug;
}
