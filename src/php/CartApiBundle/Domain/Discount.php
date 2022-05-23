<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

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
     * @var array<string, string>
     * @required
     */
    public $name;

    /**
     * @var array<string, string>
     */
    public $description;

    /**
     * Amount discounted.
     *
     * On Cart, the amount discounted in the cart.
     * On LineItem, the amount discounted per single line item.
     *
     * @var ?integer
     */
    public $discountedAmount;

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
