<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\Variant;

interface CartApi
{
    public function addToCart(Cart $cart, LineItem $lineItem): Cart;

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count): Cart;

    public function removeLineItem(Cart $cart, LineItem $lineItem): Cart;

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return mixed
     */
    public function getDangerousInnerClient();
}
