<?php

namespace Frontastic\Common\CartApiBundle\Domain;

/**
 * This interface supersedes {@link OrderIdGenerator}.
 */
interface OrderIdGeneratorV2
{
    public function getOrderId(CartApi $cartApi, Cart $cart): string;
}
