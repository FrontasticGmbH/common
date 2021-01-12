<?php

namespace Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;

use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGeneratorV2;

class Random implements OrderIdGeneratorV2
{
    public function getOrderId(CartApi $cartApi, Cart $cart): string
    {
        return substr(md5(microtime()), 2, 8);
    }
}
