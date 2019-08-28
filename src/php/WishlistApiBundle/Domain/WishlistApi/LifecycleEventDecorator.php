<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\LifecycleTrait;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
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
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getWishlist(string $wishlistId, string $locale): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $anonymousId
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getAnonymous(string $anonymousId, string $locale): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $accountId
     * @param string $locale
     * @return array
     */
    public function getWishlists(string $accountId, string $locale): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function create(Wishlist $wishlist, string $locale): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param array $lineItems
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function addMultipleToWishlist(Wishlist $wishlist, array $lineItems, string $locale): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count, string $locale): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
