<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

use Frontastic\Common\AccountApiBundle\Domain\Address;

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
     * @var string
     */
    public $email;

    /**
     * @var \DateTimeImmutable
     */
    public $birthday;

    /**
     * @var ?ShippingMethod
     */
    public $shippingMethod;

    /**
     * @var ?Address
     */
    public $shippingAddress;

    /**
     * @var ?Address
     */
    public $billingAddress;

    /**
     * @var integer
     */
    public $sum = 0;

    /**
     * @var ?Payment
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
