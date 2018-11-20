<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class LifecycleEventDecorator implements CartApi
{
    private $aggregate;
    private $listerners = [];

    public function __construct(CartApi $aggregate, iterable $listerners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listerners as $listerner) {
            $this->addListener($listerner);
        }
    }

    public function addListener($listener)
    {
        $this->listerners[] = $listener;
    }

    public function getForUser(string $userId): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getAnonymous(string $anonymousId): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function addToCart(Cart $cart, LineItem $lineItem): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function removeLineItem(Cart $cart, LineItem $lineItem): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setEmail(Cart $cart, string $email): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setShippingAddress(Cart $cart, array $address): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setBillingAddress(Cart $cart, array $address): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function setPayment(Cart $cart, Payment $payment): Cart
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function order(Cart $cart): Order
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getOrder(string $orderId): Order
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getOrders(string $accountId): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * This method is a temporary hack to recieve new orders. The
     * synchronization is based on a locally stored sequence number.
     */
    public function getNewOrders(): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function startTransaction(Cart $cart)
    {
        return $this->aggregate->startTransaction($cart);
    }

    public function commit()
    {
        return $this->aggregate->commit();
    }

    public function getDangerousInnerClient()
    {
        return $this->aggregate->getDangerousInnerClient();
    }

    private function dispatch(string $method, array $arguments)
    {
        $beforeEvent = 'before' . ucfirst($method);
        foreach ($this->listerners as $listener) {
            if (is_callable([$listener, $beforeEvent])) {
                call_user_func_array([$listener, $beforeEvent], array_merge([$this->aggregate], $arguments));
            }
        }

        $result = call_user_func_array([$this->aggregate, $method], $arguments);

        $afterEvent = 'after' . ucfirst($method);
        foreach ($this->listerners as $listener) {
            if (is_callable([$listener, $afterEvent])) {
                $listener->$afterEvent($this->aggregate, $result);
            }
        }

        return $result;
    }
}
