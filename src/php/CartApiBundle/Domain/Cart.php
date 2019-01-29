<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

class Cart extends DataObject
{
    /**
     * @var string
     */
    public $cartId;

    /**
     * @var string
     */
    public $cartVersion;

    /**
     * @var \Frontastic\Common\CartApiBundle\Domain\LineItem[]
     */
    public $lineItems = [];

    /**
     * @var integer
     */
    public $sum = 0;

    /**
     * @var Payment
     */
    public $payment;

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
    public $dangerousInnerCart;
}
