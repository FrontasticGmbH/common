<?php

namespace Frontastic\Common\CartApiBundle\Domain;

interface CartApi
{
    /**
     * @param string $userId
     * @param string $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getForUser(string $userId, string $locale): Cart;


    /**
     * @param string $anonymousId
     * @param string $locale
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getAnonymous(string $anonymousId, string $locale): Cart;

    /**
     * @param string $cartId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \RuntimeExcption if cart with $cartId was not found
     */
    public function getById(string $cartId): Cart;

    /**
     * @param array $lineItemType
     * @fixme Is this a hard CT dependency?
     */
    public function setCustomLineItemType(array $lineItemType): void;

    /**
     * @return array
     * @fixme Is this a hard CT dependency?
     */
    public function getCustomLineItemType(): array;

    /**
     * @param array $taxCategory
     */
    public function setTaxCategory(array $taxCategory): void;

    /**
     * @return array
     */
    public function getTaxCategory(): array;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function addToCart(Cart $cart, LineItem $lineItem): Cart;

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count, ?array $custom = null): Cart;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function removeLineItem(Cart $cart, LineItem $lineItem): Cart;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $email
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setEmail(Cart $cart, string $email): Cart;

    public function setShippingMethod(Cart $cart, string $shippingMethod): Cart;

    public function setCustomField(Cart $cart, array $fields): Cart;

    public function setCustomType(Cart $cart, string $id): Cart;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setShippingAddress(Cart $cart, array $address): Cart;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setBillingAddress(Cart $cart, array $address): Cart;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\Payment $payment
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null): Cart;

    public function redeemDiscountCode(Cart $cart, string $code): Cart;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $discountId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function removeDiscountCode(Cart $cart, string $discountId): Cart;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    public function order(Cart $cart): Order;

    /**
     * @param string $orderId
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    public function getOrder(string $orderId): Order;

    /**
     * @param string $accountId
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     */
    public function getOrders(string $accountId): array;

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     */
    public function startTransaction(Cart $cart): void;

    /**
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function commit(): Cart;

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
