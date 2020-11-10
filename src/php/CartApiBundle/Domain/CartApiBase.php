<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;

abstract class CartApiBase implements CartApi
{
    public function getForUser(Account $account, string $locale): Cart
    {
        return $this->getForUserImplementation($account, $locale);
    }

    public function getAnonymous(string $anonymousId, string $locale): Cart
    {
        return $this->getAnonymousImplementation($anonymousId, $locale);
    }

    public function getById(string $cartId, string $locale = null): Cart
    {
        return $this->getByIdImplementation($cartId, $locale);
    }

    public function setCustomLineItemType(array $lineItemType): void
    {
        $this->setCustomLineItemTypeImplementation($lineItemType);
    }

    public function getCustomLineItemType(): array
    {
        return $this->getCustomLineItemTypeImplementation();
    }

    public function setTaxCategory(array $taxCategory): void
    {
        $this->setTaxCategoryImplementation($taxCategory);
    }

    public function getTaxCategory(): ?array
    {
        return $this->getTaxCategoryImplementation();
    }

    public function addToCart(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        return $this->addToCartImplementation($cart, $lineItem, $locale);
    }

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count, ?array $custom = null, string $locale = null): Cart
    {
        return $this->updateLineItemImplementation($cart, $lineItem, $count, $custom, $locale);
    }

    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        return $this->removeLineItemImplementation($cart, $lineItem, $locale);
    }

    public function setEmail(Cart $cart, string $email, string $locale = null): Cart
    {
        return $this->setEmailImplementation($cart, $email, $locale);
    }

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        return $this->setShippingMethodImplementation($cart, $shippingMethod, $locale);
    }

    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart
    {
        return $this->setCustomFieldImplementation($cart, $fields, $locale);
    }

    public function setRawApiInput(Cart $cart, string $locale = null): Cart
    {
        return $this->setRawApiInputImplementation($cart, $locale);
    }

    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        return $this->setShippingAddressImplementation($cart, $address, $locale);
    }

    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        return $this->setBillingAddressImplementation($cart, $address, $locale);
    }

    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart
    {
        return $this->addPaymentImplementation($cart, $payment, $custom, $locale);
    }

    public function updatePayment(Cart $cart, Payment $payment, string $localeString): Payment
    {
       return $this->updatePaymentImplementation($cart, $payment, $localeString);
    }

    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart
    {
        return $this->redeemDiscountCodeImplementation($cart, $code, $locale);
    }

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart
    {
        return $this->removeDiscountCodeImplementation($cart, $discountLineItem, $locale);
    }

    public function order(Cart $cart, string $locale = null): Order
    {
        return $this->orderImplementation($cart, $locale);
    }

    public function getOrder(Account $account, string $orderId, string $locale = null): Order
    {
        return $this->getOrderImplementation($account, $orderId, $locale);
    }

    public function getOrders(Account $account, string $locale = null): array
    {
        return $this->getOrdersImplementation($account, $locale);
    }

    public function startTransaction(Cart $cart): void
    {
        $this->startTransactionImplementation($cart);
    }

    public function commit(string $locale = null): Cart
    {
        return $this->commitImplementation($locale);
    }

    abstract protected function getForUserImplementation(Account $account, string $locale): Cart;

    abstract protected function getAnonymousImplementation(string $anonymousId, string $locale): Cart;

    abstract protected function getByIdImplementation(string $cartId, string $locale = null): Cart;

    abstract protected function setCustomLineItemTypeImplementation(array $lineItemType): void;

    abstract protected function getCustomLineItemTypeImplementation(): array;

    abstract protected function setTaxCategoryImplementation(array $taxCategory): void;

    abstract protected function getTaxCategoryImplementation(): ?array;

    abstract protected function addToCartImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart;

    abstract protected function updateLineItemImplementation(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart;

    abstract protected function removeLineItemImplementation(
        Cart $cart,
        LineItem $lineItem,
        string $locale = null
    ): Cart;

    abstract protected function setEmailImplementation(Cart $cart, string $email, string $locale = null): Cart;

    abstract protected function setShippingMethodImplementation(
        Cart $cart,
        string $shippingMethod,
        string $locale = null
    ): Cart;

    abstract protected function setCustomFieldImplementation(Cart $cart, array $fields, string $locale = null): Cart;

    abstract protected function setRawApiInputImplementation(Cart $cart, string $locale = null): Cart;

    abstract protected function setShippingAddressImplementation(
        Cart $cart,
        Address $address,
        string $locale = null
    ): Cart;

    abstract protected function setBillingAddressImplementation(
        Cart $cart,
        Address $address,
        string $locale = null
    ): Cart;

    abstract protected function addPaymentImplementation(
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): Cart;

    abstract protected function updatePaymentImplementation(
        Cart $cart,
        Payment $payment,
        string $localeString
    ): Payment;

    abstract protected function redeemDiscountCodeImplementation(Cart $cart, string $code, string $locale = null): Cart;

    abstract protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $locale = null
    ): Cart;

    abstract protected function orderImplementation(Cart $cart, string $locale = null): Order;

    abstract protected function getOrderImplementation(Account $account, string $orderId, string $locale = null): Order;

    abstract protected function getOrdersImplementation(Account $account, string $locale = null): array;

    abstract protected function startTransactionImplementation(Cart $cart): void;

    abstract protected function commitImplementation(string $locale = null): Cart;
}
