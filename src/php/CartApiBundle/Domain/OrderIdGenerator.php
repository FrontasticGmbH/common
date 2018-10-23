<?php

namespace Frontastic\Common\CartApiBundle\Domain;

interface OrderIdGenerator
{
    public function getOrderId(Cart $cart): string;
}
