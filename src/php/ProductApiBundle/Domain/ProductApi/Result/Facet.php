<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

use Kore\DataObject\DataObject;

abstract class Facet extends DataObject
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var string
     */
    public $key;

    /**
     * @var boolean
     */
    public $selected = false;
}
