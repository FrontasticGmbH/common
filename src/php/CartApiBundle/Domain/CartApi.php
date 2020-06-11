<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;

interface CartApi
{
    public function getForUser(Account $account, string $locale): Cart;

    public function getAnonymous(string $anonymousId, string $locale): Cart;

    /**
     * @throws \RuntimeException if cart with $cartId was not found
     */
    public function getById(string $cartId, string $locale = null): Cart;

    /**
     * @fixme Is this a hard CT dependency?
     */
    public function setCustomLineItemType(array $lineItemType): void;

    /**
     * @fixme Is this a hard CT dependency?
     */
    public function getCustomLineItemType(): array;

    /**
     * @fixme Is this a hard CT dependency?
     */
    public function setTaxCategory(array $taxCategory): void;

    /**
     * @fixme Is this a hard CT dependency?
     */
    public function getTaxCategory(): array;

    public function addToCart(Cart $cart, LineItem $lineItem, string $locale = null): Cart;

    public function updateLineItem(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart;

    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart;

    public function setEmail(Cart $cart, string $email, string $locale = null): Cart;

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart;

    /** @deprecated  */
    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart;

    public function setRawApiInput(Cart $cart, string $locale = null): Cart;

    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart;

    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart;

    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart;

    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart;

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart;

    public function order(Cart $cart, string $locale = null): Order;

    public function getOrder(Account $account, string $orderId, string $locale = null): Order;

    /**
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     */
    public function getOrders(Account $account, string $locale = null): array;

    public function startTransaction(Cart $cart): void;

    public function commit(string $locale = null): Cart;

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
