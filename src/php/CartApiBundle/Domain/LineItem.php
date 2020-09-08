<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class LineItem extends ApiDataObject
{
    /**
     * @var string
     */
    public $lineItemId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var integer
     */
    public $count = 0;

    /**
     * @var integer
     */
    public $price = 0;

    /**
     * @var integer
     */
    public $discountedPrice;

    /**
     * Translatable discount texts, if any are applied
     *
     * @var array
     */
    public $discountTexts = [];

    /**
     * @var integer
     */
    public $totalPrice = 0;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var bool
     */
    public $isGift = false;

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
    public $dangerousInnerItem;
}
