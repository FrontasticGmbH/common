<?php

namespace Frontastic\Common\CartApiBundle\Domain;

/**
 * @type
 */
class Order extends Cart
{
    /**
     * @var string
     */
    public $orderId;

    /**
     * @var string
     */
    public $orderVersion;

    /**
     * @var string
     */
    public $orderState;

    /**
     * @var \DateTimeImmutable
     */
    public $createdAt;

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
    public $dangerousInnerOrder;
}
