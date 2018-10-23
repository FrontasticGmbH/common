<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Facets;

class TermFacet extends Facet
{
    /**
     * @var string
     */
    public $type = Facets::TYPE_TERM;

    /**
     * @var Term[]
     */
    public $terms = [];
}
