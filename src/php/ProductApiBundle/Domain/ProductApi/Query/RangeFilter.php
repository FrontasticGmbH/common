<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

/**
 * @type
 */
class RangeFilter extends Filter
{
    /**
     * @var ?integer
     */
    public $min;

    /**
     * @var ?integer
     */
    public $max;
}
