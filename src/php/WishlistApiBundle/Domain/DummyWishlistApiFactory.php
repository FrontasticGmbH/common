<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\DummyWishlistApi;

class DummyWishlistApiFactory implements WishlistApiFactory
{
    public function factor(Project $project): WishlistApi
    {
        return new DummyWishlistApi();
    }
}
