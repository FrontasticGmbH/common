<?php

namespace Frontastic\Common\SprykerBundle\Domain\Wishlist\Expander;

use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;

interface WishlistExpanderInterface
{
    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param array $includes
     *
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function expand(Wishlist $wishlist, array $includes): Wishlist;
}
