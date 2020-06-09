<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Facets;

/**
 * @type
 */
class RangeFacet extends Facet
{
    /**
     * @var string
     */
    public $type = Facets::TYPE_RANGE;

    /**
     * @var integer
     */
    public $min = 0;

    /**
     * @var integer
     */
    public $max = PHP_INT_MAX;
}
