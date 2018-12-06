<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\LifecycleTrait;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\Payment;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;

/**
 * Class LifecycleEventDecorator
 *
 * @package Frontastic\Common\WishlistApiBundle\Domain\WishlistApi
 */
class LifecycleEventDecorator implements WishlistApi
{
    use LifecycleTrait;

    /**
     * @var \Frontastic\Common\WishlistApiBundle\Domain\WishlistApi
     */
    private $aggregate;

    /**
     * LifecycleEventDecorator constructor.
     *
     * @param \Frontastic\Common\WishlistApiBundle\Domain\WishlistApi $aggregate
     * @param iterable $listeners
     */
    public function __construct(WishlistApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    /**
     * @return \Frontastic\Common\WishlistApiBundle\Domain\WishlistApi
     */
    protected function getAggregate(): object
    {
        return $this->aggregate;
    }

    /**
     * @param string $wishlistId
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getWishlist(string $wishlistId): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $anonymousId
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getAnonymous(string $anonymousId): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @return array
     */
    public function getWishlists(string $accountId): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function create(Wishlist $wishlist): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
