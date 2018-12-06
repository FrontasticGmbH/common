<?php

namespace Frontastic\Common\CartApiBundle\Domain;

interface OrderIdGenerator
{
    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @return string
     */
    public function getOrderId(Cart $cart): string;
}
