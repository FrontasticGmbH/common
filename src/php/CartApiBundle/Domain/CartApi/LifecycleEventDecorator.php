<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
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
     * @var \Frontastic\Common\CartApiBundle\Domain\CartApi
     */
    private $aggregate;

    /**
     * LifecycleEventDecorator constructor.
     *
     * @param \Frontastic\Common\CartApiBundle\Domain\CartApi $aggregate
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
     * @return \Frontastic\Common\CartApiBundle\Domain\CartApi
     */
    protected function getAggregate(): object
    {
        return $this->aggregate;
    }

    /**
     * @param string $userId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getForUser(string $userId): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $anonymousId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function getAnonymous(string $anonymousId): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $lineItemType
     */
    public function setCustomLineItemType(array $lineItemType): void
    {
        $this->aggregate->setCustomLineItemType($lineItemType);
    }

    /**
     * @return array
     */
    public function getCustomLineItemType(): array
    {
        return $this->aggregate->getCustomLineItemType();
    }

    /**
     * @param array $taxCategory
     */
    public function setTaxCategory(array $taxCategory): void
    {
        $this->aggregate->setTaxCategory($taxCategory);
    }

    /**
     * @return array
     */
    public function getTaxCategory(): array
    {
        return $this->aggregate->getTaxCategory();
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function addToCart(Cart $cart, LineItem $lineItem): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function removeLineItem(Cart $cart, LineItem $lineItem): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $email
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setEmail(Cart $cart, string $email): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setShippingAddress(Cart $cart, array $address): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setBillingAddress(Cart $cart, array $address): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\Payment $payment
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setPayment(Cart $cart, Payment $payment): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    public function order(Cart $cart): Order
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $orderId
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    public function getOrder(string $orderId): Order
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     */
    public function getOrders(string $accountId): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * This method is a temporary hack to recieve new orders. The
     * synchronization is based on a locally stored sequence number.
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     */
    public function getNewOrders(): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     */
    public function startTransaction(Cart $cart): void
    {
        $this->aggregate->startTransaction($cart);
    }

    /**
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function commit(): Cart
    {
        return $this->aggregate->commit();
    }
}
