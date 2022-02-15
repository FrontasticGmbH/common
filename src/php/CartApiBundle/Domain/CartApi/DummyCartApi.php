<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\CartApiBase;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreator;

class DummyCartApi extends CartApiBase
{
    protected function getForUserImplementation(Account $account, string $localeString): Cart
    {
        $this->doNotUse();
    }

    protected function getAnonymousImplementation(string $anonymousId, string $localeString): Cart
    {
        $this->doNotUse();
    }

    protected function getByIdImplementation(string $cartId, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function addToCartImplementation(Cart $cart, LineItem $lineItem, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function updateLineItemImplementation(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $localeString = null
    ): Cart {
        $this->doNotUse();
    }

    protected function removeLineItemImplementation(Cart $cart, LineItem $lineItem, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function setEmailImplementation(Cart $cart, string $email, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function setShippingMethodImplementation(
        Cart $cart,
        string $shippingMethod,
        string $localeString = null
    ): Cart {
        $this->doNotUse();
    }

    protected function setCustomFieldImplementation(Cart $cart, array $fields, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function setRawApiInputImplementation(Cart $cart, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function setShippingAddressImplementation(Cart $cart, Address $address, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function setBillingAddressImplementation(Cart $cart, Address $address, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function addPaymentImplementation(
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $localeString = null
    ): Cart {
        $this->doNotUse();
    }

    protected function updatePaymentImplementation(Cart $cart, Payment $payment, string $localeString): Payment
    {
        $this->doNotUse();
    }

    protected function redeemDiscountCodeImplementation(Cart $cart, string $code, string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $localeString = null
    ): Cart {
        $this->doNotUse();
    }

    protected function orderImplementation(Cart $cart, string $locale = null): Order
    {
        $this->doNotUse();
    }

    protected function getOrderImplementation(Account $account, string $orderId, string $locale = null): Order
    {
        $this->doNotUse();
    }

    protected function getOrdersImplementation(Account $account, string $locale = null): array
    {
        $this->doNotUse();
    }

    protected function postCartActions(Cart $cart, array $actions, CommercetoolsLocale $locale): Cart
    {
        $this->doNotUse();
    }

    protected function startTransactionImplementation(Cart $cart): void
    {
        $this->doNotUse();
    }

    protected function commitImplementation(string $localeString = null): Cart
    {
        $this->doNotUse();
    }

    public function getAvailableShippingMethodsImplementation(Cart $cart, string $localeString): array
    {
        $this->doNotUse();
    }

    public function getShippingMethodsImplementation(string $localeString, bool $onlyMatching = false): array
    {
        $this->doNotUse();
    }

    public function getDangerousInnerClient()
    {
        $this->doNotUse();
    }

    public function getDangerousInnerMapper(): CartApi\Commercetools\Mapper
    {
        $this->doNotUse();
    }

    public function getDangerousInnerLocaleCreator(): CommercetoolsLocaleCreator
    {
        $this->doNotUse();
    }

    protected function setCustomLineItemTypeImplementation(array $lineItemType): void
    {
        $this->doNotUse();
    }

    protected function getCustomLineItemTypeImplementation(): array
    {
        $this->doNotUse();
    }

    protected function setTaxCategoryImplementation(array $taxCategory): void
    {
        $this->doNotUse();
    }

    protected function getTaxCategoryImplementation(): ?array
    {
        $this->doNotUse();
    }

    public function updatePaymentStatus(Payment $payment): void
    {
        $this->doNotUse();
    }

    public function getPayment(string $paymentId): ?Payment
    {
        $this->doNotUse();
    }

    public function updatePaymentInterfaceId(Payment $payment): void
    {
        $this->doNotUse();
    }

    private function doNotUse(): void
    {
        throw new \Exception("CartApi is not available for Nextjs projects.");
    }
}
