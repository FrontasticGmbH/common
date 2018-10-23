<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

use Kore\DataObject\DataObject;

class Term extends DataObject
{
    /**
     * @var string
     */
    public $handle;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    /**
     * @var integer
     */
    public $count;

    /**
     * @var boolean
     */
    public $selected;
}
