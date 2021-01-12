<?php

namespace Frontastic\Common\CartApiBundle\Domain;

class OrderIdGeneratorV2Adapter implements OrderIdGeneratorV2
{
    /**
     * @var OrderIdGenerator
     */
    private OrderIdGenerator $legacyIdGenerator;

    public function __construct(OrderIdGenerator $legacyIdGenerator)
    {
        $this->legacyIdGenerator = $legacyIdGenerator;
    }

    public function getOrderId(CartApi $cartApi, Cart $cart): string
    {
        return $this->legacyIdGenerator->getOrderId($cart);
    }
}
