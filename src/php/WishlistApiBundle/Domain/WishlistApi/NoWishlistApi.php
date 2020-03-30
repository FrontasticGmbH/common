<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

/**
 * This is a dummy implementation. It always returns an empty wishlist and throws an exception when trying to modify
 * the wishlist. It can be used when no wishlist API is available.
 */
class NoWishlistApi implements WishlistApi
{
    public function getWishlist(string $wishlistId, string $locale): Wishlist
    {
        return new Wishlist([
            'wishlistId' => $wishlistId,
            'wishlistVersion' => '1',
        ]);
    }

    public function getAnonymous(string $anonymousId, string $locale): Wishlist
    {
        return new Wishlist([
            'wishlistId' => uniqid(),
            'wishlistVersion' => '1',
            'anonymousId' => $anonymousId,
        ]);
    }

    public function getWishlists(string $accountId, string $locale): array
    {
        return [
            new Wishlist([
                'wishlistId' => uniqid(),
                'wishlistVersion' => '1',
                'accountId' => $accountId,
            ]),
        ];
    }

    public function create(Wishlist $wishlist, string $locale): Wishlist
    {
        return $wishlist;
    }

    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        throw new WishlistImmutableException();
    }

    public function addMultipleToWishlist(Wishlist $wishlist, array $lineItems, string $locale): Wishlist
    {
        throw new WishlistImmutableException();
    }

    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count, string $locale): Wishlist
    {
        throw new WishlistImmutableException();
    }

    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        throw new WishlistImmutableException();
    }

    public function getDangerousInnerClient()
    {
        return null;
    }
}
