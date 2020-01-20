<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

class CategoryQuery extends Query
{
    /**
     * @var string
     */
    public $parentId;

    /**
     * @var string
     */
    public $slug;
}
