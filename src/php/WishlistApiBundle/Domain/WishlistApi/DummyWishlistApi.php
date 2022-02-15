<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

class DummyWishlistApi implements WishlistApi
{

    public function getWishlist(string $wishlistId, string $locale): Wishlist
    {
        throw $this->exception();
    }

    public function getAnonymous(string $anonymousId, string $locale): Wishlist
    {
        throw $this->exception();
    }

    public function getWishlists(string $accountId, string $locale): array
    {
        throw $this->exception();
    }

    public function create(Wishlist $wishlist, string $locale): Wishlist
    {
        throw $this->exception();
    }

    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        throw $this->exception();
    }

    public function addMultipleToWishlist(Wishlist $wishlist, array $lineItems, string $locale): Wishlist
    {
        throw $this->exception();
    }

    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count, string $locale): Wishlist
    {
        throw $this->exception();
    }

    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        throw $this->exception();
    }

    public function getDangerousInnerClient()
    {
        throw $this->exception();
    }

    private function exception(): \Throwable
    {
        return new \Exception("WishlistApi is not available for Nextjs projects.");
    }
}
