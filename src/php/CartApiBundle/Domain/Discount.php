<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;
use Frontastic\Common\Translatable;

/**
 * @type
 */
class Discount extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $discountId;

    /**
     * @var string
     * @required
     */
    public $code;

    /**
     * @var string
     * @required
     */
    public $state;

    /**
     * @var Translatable
     * @required
     */
    public $name;

    /**
     * @var Translatable
     */
    public $description;

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerDiscount;
}
