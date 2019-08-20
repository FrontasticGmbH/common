<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

class RangeFilter extends Filter
{
    /**
     * @var integer?
     */
    public $min;

    /**
     * @var integer?
     */
    public $max;
}
