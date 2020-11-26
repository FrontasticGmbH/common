<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Term extends ApiDataObject
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
