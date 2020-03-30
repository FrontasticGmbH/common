<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;

class WishlistImmutableException extends Exception
{

    public function __construct()
    {
        parent::__construct('modifying the wishlist is not supported');
    }
}
