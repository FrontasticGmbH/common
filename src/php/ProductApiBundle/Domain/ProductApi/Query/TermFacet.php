<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Facets;

class TermFacet extends Facet
{
    /**
     * @var string
     */
    public $type = Facets::TYPE_TERM;

    /**
     * @var string[]
     */
    public $terms = [];
}
