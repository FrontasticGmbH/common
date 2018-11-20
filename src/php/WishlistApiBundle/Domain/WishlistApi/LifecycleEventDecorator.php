<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\Payment;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class LifecycleEventDecorator implements WishlistApi
{
    private $aggregate;
    private $listerners = [];

    public function __construct(WishlistApi $aggregate, iterable $listerners = [])
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

    public function getWishlist(string $orderId): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getAnonymous(string $anonymousId): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }


    public function getWishlists(string $accountId): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function create(Wishlist $wishlist): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
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
