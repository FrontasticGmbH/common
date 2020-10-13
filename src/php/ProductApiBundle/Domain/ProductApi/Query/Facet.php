<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Kore\DataObject\DataObject;

/**
 * @type
 */
abstract class Facet extends DataObject
{
    /**
     * @var string
     */
    public $handle;
}
