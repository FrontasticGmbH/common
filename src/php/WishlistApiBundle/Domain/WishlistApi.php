<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;

interface WishlistApi
{
    /**
     * @param string $wishlistId
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getWishlist(string $wishlistId, string $locale): Wishlist;

    /**
     * @param string $anonymousId
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getAnonymous(string $anonymousId, string $locale): Wishlist;

    /**
     * @param string $accountId
     * @param string $locale
     * @return array
     */
    public function getWishlists(string $accountId, string $locale): array;

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function create(Wishlist $wishlist): Wishlist;

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist;

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count): Wishlist;

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem): Wishlist;

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return mixed
     */
    public function getDangerousInnerClient();
}
