<?php

namespace Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;

use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;
use Frontastic\Common\CartApiBundle\Domain\Cart;

class Random implements OrderIdGenerator
{
    public function getOrderId(Cart $cart): string
    {
        return substr(md5(microtime()), 2, 8);
    }
}
