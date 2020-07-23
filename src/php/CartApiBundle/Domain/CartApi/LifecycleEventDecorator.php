<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\UpdatePaymentCommand;
use Frontastic\Common\LifecycleTrait;

/**
 * Class LifecycleEventDecorator
 *
 * @package Frontastic\Common\CartApiBundle\Domain\CartApi
 */
class LifecycleEventDecorator implements CartApi
{
    use LifecycleTrait;

    /**
     * @var CartApi
     */
    private $aggregate;

    /**
     * LifecycleEventDecorator constructor.
     *
     * @param CartApi $aggregate
     * @param iterable $listeners
     */
    public function __construct(CartApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    /**
     * @return CartApi
     */
    protected function getAggregate(): object
    {
        return $this->aggregate;
    }

    public function getForUser(Account $account, string $locale): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getById(string $cartId, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getAnonymous(string $anonymousId, string $locale): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setCustomLineItemType(array $lineItemType): void
    {
        $this->aggregate->setCustomLineItemType($lineItemType);
    }

    public function getCustomLineItemType(): array
    {
        return $this->aggregate->getCustomLineItemType();
    }

    public function setTaxCategory(array $taxCategory): void
    {
        $this->aggregate->setTaxCategory($taxCategory);
    }

    public function getTaxCategory(): array
    {
        return $this->aggregate->getTaxCategory();
    }

    public function addToCart(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updateLineItem(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setEmail(Cart $cart, string $email, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setRawApiInput(Cart $cart, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updatePayment(Cart $cart, Payment $payment, string $localeString): Payment
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function order(Cart $cart, string $locale = null): Order
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getOrder(Account $account, string $orderId, string $locale = null): Order
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @return Order[]
     */
    public function getOrders(Account $account, string $locale = null): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * This method is a temporary hack to recieve new orders. The
     * synchronization is based on a locally stored sequence number.
     *
     * @return Order[]
     */
    public function getNewOrders(): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function startTransaction(Cart $cart): void
    {
        $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function commit(string $locale = null): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
