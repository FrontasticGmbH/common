<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Facets;

class RangeFacet extends Facet
{
    /**
     * @var string
     */
    public $type = Facets::TYPE_RANGE;

    /**
     * @var integer
     */
    public $min;

    /**
     * @var integer
     */
    public $max;

    /**
     * @var integer
     */
    public $step;

    /**
     * @var array
     */
    public $value = ['min' => 0, 'max' => 0];
}
