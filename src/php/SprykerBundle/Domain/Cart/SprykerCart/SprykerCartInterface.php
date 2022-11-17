<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\LineItem\Variant;

interface SprykerCartInterface
{

    public function getById(string $cartId, string $locale = null): Cart;

    /**
     * @param string|null $id
     * @param string|null $locale
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getCart(?string $id = null, ?string $locale = null): Cart;

    /**
     * @param Cart $cart
     * @param Variant $lineItem
     * @return Cart
     */
    public function addLineItemToCart(Cart $cart, Variant $lineItem): Cart;

    /**
     * @param Cart $cart
     * @param Variant $lineItem
     * @param int $count
     * @return Cart
     */
    public function updateLineItem(Cart $cart, Variant $lineItem, int $count): Cart;

    /**
     * @param Cart $cart
     * @param Variant $lineItem
     * @return Cart
     */
    public function removeLineItem(Cart $cart, Variant $lineItem): Cart;

    public function redeemDiscount(Cart $cart, string $code, string $locale = null): Cart;

    public function removeDiscount(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart;
}
