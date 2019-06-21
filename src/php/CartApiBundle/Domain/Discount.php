<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

class Discount extends DataObject
{
    /**
     * @var string
     */
    public $discountId;

    /**
     * @var Translatable
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