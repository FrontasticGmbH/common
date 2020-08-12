<?php

namespace Frontastic\Common\SprykerBundle\BaseApi;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\SprykerBundle\Domain\Cart\Expander\CartExpanderInterface;

trait CartExpandingTrait
{
    /**
     * @var array|\Frontastic\Common\SprykerBundle\Domain\Cart\Expander\CartExpanderInterface[]
     */
    private $cartExpanders = [];

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\Cart\Expander\CartExpanderInterface $expander
     *
     * @return CartExpandingTrait
     */
    public function registerCartExpander(CartExpanderInterface $expander): self
    {
        $this->cartExpanders[] = $expander;

        return $this;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $includedResources
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function expandCart(Cart $cart, array $includedResources = []): Cart
    {
        foreach ($this->cartExpanders as $expander) {
            $expander->expand($cart, $includedResources);
        }

        return $cart;
    }
}
