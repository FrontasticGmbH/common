<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

use Kore\DataObject\DataObject;

/**
 * @type
 */
abstract class Facet extends DataObject
{
    /**
     * @var string
     * @required
     */
    public $type;

    /**
     * @var string
     * @required
     */
    public $handle;

    /**
     * @var string
     * @required
     */
    public $key;

    /**
     * @var boolean
     * @required
     */
    public $selected = false;
}
