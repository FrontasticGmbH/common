<?php

namespace Frontastic\Common\CartApiBundle\Domain;

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
}
