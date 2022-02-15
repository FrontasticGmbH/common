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
        throw $this->exception();
    }

    protected function getAnonymousImplementation(string $anonymousId, string $localeString): Cart
    {
        throw $this->exception();
    }

    protected function getByIdImplementation(string $cartId, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function addToCartImplementation(Cart $cart, LineItem $lineItem, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function updateLineItemImplementation(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $localeString = null
    ): Cart {
        throw $this->exception();
    }

    protected function removeLineItemImplementation(Cart $cart, LineItem $lineItem, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function setEmailImplementation(Cart $cart, string $email, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function setShippingMethodImplementation(
        Cart $cart,
        string $shippingMethod,
        string $localeString = null
    ): Cart {
        throw $this->exception();
    }

    protected function setCustomFieldImplementation(Cart $cart, array $fields, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function setRawApiInputImplementation(Cart $cart, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function setShippingAddressImplementation(Cart $cart, Address $address, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function setBillingAddressImplementation(Cart $cart, Address $address, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function addPaymentImplementation(
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $localeString = null
    ): Cart {
        throw $this->exception();
    }

    protected function updatePaymentImplementation(Cart $cart, Payment $payment, string $localeString): Payment
    {
        throw $this->exception();
    }

    protected function redeemDiscountCodeImplementation(Cart $cart, string $code, string $localeString = null): Cart
    {
        throw $this->exception();
    }

    protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $localeString = null
    ): Cart {
        throw $this->exception();
    }

    protected function orderImplementation(Cart $cart, string $locale = null): Order
    {
        throw $this->exception();
    }

    protected function getOrderImplementation(Account $account, string $orderId, string $locale = null): Order
    {
        throw $this->exception();
    }

    protected function getOrdersImplementation(Account $account, string $locale = null): array
    {
        throw $this->exception();
    }

    protected function postCartActions(Cart $cart, array $actions, CommercetoolsLocale $locale): Cart
    {
        throw $this->exception();
    }

    protected function startTransactionImplementation(Cart $cart): void
    {
        throw $this->exception();
    }

    protected function commitImplementation(string $localeString = null): Cart
    {
        throw $this->exception();
    }

    public function getAvailableShippingMethodsImplementation(Cart $cart, string $localeString): array
    {
        throw $this->exception();
    }

    public function getShippingMethodsImplementation(string $localeString, bool $onlyMatching = false): array
    {
        throw $this->exception();
    }

    public function getDangerousInnerClient()
    {
        throw $this->exception();
    }

    public function getDangerousInnerMapper(): CartApi\Commercetools\Mapper
    {
        throw $this->exception();
    }

    public function getDangerousInnerLocaleCreator(): CommercetoolsLocaleCreator
    {
        throw $this->exception();
    }

    protected function setCustomLineItemTypeImplementation(array $lineItemType): void
    {
        throw $this->exception();
    }

    protected function getCustomLineItemTypeImplementation(): array
    {
        throw $this->exception();
    }

    protected function setTaxCategoryImplementation(array $taxCategory): void
    {
        throw $this->exception();
    }

    protected function getTaxCategoryImplementation(): ?array
    {
        throw $this->exception();
    }

    public function updatePaymentStatus(Payment $payment): void
    {
        throw $this->exception();
    }

    public function getPayment(string $paymentId): ?Payment
    {
        throw $this->exception();
    }

    public function updatePaymentInterfaceId(Payment $payment): void
    {
        throw $this->exception();
    }

    private function exception(): \Throwable
    {
        return new \Exception("CartApi is not available for Nextjs projects.");
    }
}
