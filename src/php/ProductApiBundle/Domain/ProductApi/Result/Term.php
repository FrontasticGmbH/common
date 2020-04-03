<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

use Kore\DataObject\DataObject;

class Term extends DataObject
{
    /**
     * Internal identifier. Depending on the backend it maybe equal to $name.
     *
     * @var string
     */
    public $handle;

    /**
     * Human readable name
     *
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
