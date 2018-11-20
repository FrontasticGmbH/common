<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\Variant;

interface WishlistApi
{
    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem): Wishlist;

    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count): Wishlist;

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
