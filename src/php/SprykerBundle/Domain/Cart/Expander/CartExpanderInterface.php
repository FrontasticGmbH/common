<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Expander;

use Frontastic\Common\CartApiBundle\Domain\Cart;

interface CartExpanderInterface
{
    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array|\WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $includes
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function expand(Cart $cart, array $includes): Cart;
}
